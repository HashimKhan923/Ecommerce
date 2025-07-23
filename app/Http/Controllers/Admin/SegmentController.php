<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Segment;
use App\Models\User;


class SegmentController extends Controller
{
   public function index()
    {
        $segments = Segment::whereNull('seller_id')->get();
        $totalUsers = User::count();

        // Preload all users once to avoid N+1
        $users = User::with('order')->get();

        $data = $segments->map(function ($segment) use ($totalUsers, $users) {
            $matchedUsers = $users->filter(fn($user) =>
                $this->evaluateRules($user, json_decode($segment->rules, true))
            );

            return [
                'segment' => $segment,
                'total_users' => $totalUsers,
                'matched_users_count' => $matchedUsers->count()
            ];
        });

        return response()->json(['segments' => $data]);
    }

    /**
     * Create a new admin segment (for users table).
     */
    public function create(Request $request)
    {
        $segment = Segment::create([
            'seller_id' => null,
            'name' => $request->name,
            'rules' => $request->rules, // JSON string
        ]);

        return response()->json(['segment' => $segment]);
    }

    /**
     * Update an existing admin segment.
     */
    public function update(Request $request, $id)
    {
        $segment = Segment::findOrFail($id);

        if ($segment->seller_id !== null) {
            return response()->json(['error' => 'This is not an admin segment.'], 403);
        }

        $segment->update([
            'name' => $request->name,
            'rules' => $request->rules
        ]);

        return response()->json(['segment' => $segment]);
    }

    /**
     * Apply segment rules and return matched users.
     */
    public function apply($segmentId)
    {
        $segment = Segment::findOrFail($segmentId);

        if ($segment->seller_id !== null) {
            return response()->json(['error' => 'This is not an admin segment.'], 403);
        }

        $users = User::with('order')->get();

        $matchedUsers = $users->filter(fn($user) =>
            $this->evaluateRules($user, json_decode($segment->rules, true))
        );

        return response()->json([
            'segment' => $segment,
            'matched_users' => $matchedUsers->values(),
            'count' => $matchedUsers->count()
        ]);
    }

    /**
     * Evaluate rules against a single user.
     * Compatible with AND/OR logic.
     */
    private function evaluateRules($entity, $rulesGroup)
    {
        foreach ($rulesGroup as $group) {
            $matchAll = true;

            foreach ($group as $rule) {
                $field = $rule['field'];
                $operator = $rule['operator'];
                $value = $rule['value'];

                $actualValue = data_get($entity, $field);

                if (!$this->compare($actualValue, $operator, $value)) {
                    $matchAll = false;
                    break;
                }
            }

            if ($matchAll) {
                return true; // OR condition: one group matches
            }
        }

        return false;
    }

    /**
     * Compare values with various operators.
     */
    private function compare($actual, $operator, $expected)
    {
        switch ($operator) {
            case '=':
                return $actual == $expected;
            case '!=':
                return $actual != $expected;
            case '>':
                return $actual > $expected;
            case '<':
                return $actual < $expected;
            case '>=':
                return $actual >= $expected;
            case '<=':
                return $actual <= $expected;
            case 'contains':
                return stripos($actual, $expected) !== false;
            case 'starts_with':
                return stripos($actual, $expected) === 0;
            case 'ends_with':
                return str_ends_with(strtolower($actual), strtolower($expected));
            default:
                return false;
        }
    }



        public function delete($segment_id)
        {
            Segment::find($segment_id)->delete();

            return response()->json(['message' => 'Segment deleted successfully.']);

        }

        public function multi_delete(Request $request)
        {
            Segment::whereIn('id',$request->ids)->delete();


            $response = ['status'=>true,"message" => "Segments Deleted Successfully!"];
            return response($response, 200);
        }
}
