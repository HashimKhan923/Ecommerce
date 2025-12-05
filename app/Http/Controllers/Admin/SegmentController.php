<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Segment;
use App\Models\User;
use App\Models\Subscriber;

class SegmentController extends Controller
{
    /**
     * List admin segments for given type (user / subscriber).
     * Pass ?segment_type=user or ?segment_type=subscriber
     */
public function index(Request $request)
{
    $segmentType = $request->get('segment_type', 'user'); // default: user

    // Fetch only admin segments
    $segments = Segment::whereNull('seller_id')
        ->where('segment_type', $segmentType)
        ->get();

    /**
     * Load all base data ONCE depending on segment type
     */
    if ($segmentType === 'subscriber') {
        $entities = Subscriber::all();
        $totalEntities = $entities->count();
    } else {
        $entities = User::with('order')->get();
        $totalEntities = $entities->count();
    }

    /**
     * Prepare response
     */
    $data = $segments->map(function ($segment) use ($entities, $totalEntities) {

        $rules = json_decode($segment->rules, true) ?? [];

        // Find matched based on rules
        $matched = $entities->filter(function ($entity) use ($rules) {
            return $this->evaluateRules($entity, $rules);
        });

        $matchedCount = $matched->count();

        $percentage = $totalEntities > 0
            ? round(($matchedCount / $totalEntities) * 100, 2)
            : 0;

        return [
            'id'                 => $segment->id,
            'name'               => $segment->name,
            'percentage'         => $percentage,
            'last_activity'      => $segment->updated_at?->toDateTimeString(),
            'matchedCount'       => $matchedCount,
            'totalEntityCount'   => $totalEntities
        ];
    });

    return response()->json(['data' => $data]);
}

    /**
     * Create a new admin segment (for users OR subscribers).
     * Request must send: name, rules, segment_type (user|subscriber)
     */
    public function create(Request $request)
    {
        $segmentType = $request->get('segment_type', 'user'); // default user

        $segment = Segment::create([
            'seller_id'    => null,
            'segment_type' => $segmentType,
            'name'         => $request->name,
            'rules'        => $request->rules, // JSON string
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
            'name'         => $request->name,
            'rules'        => $request->rules,
            // optional: allow changing type
            'segment_type' => $request->get('segment_type', $segment->segment_type),
        ]);

        return response()->json(['segment' => $segment]);
    }

    /**
     * Apply segment rules and return matched entities (users or subscribers).
     */
    public function apply($segmentId)
    {
        $segment = Segment::findOrFail($segmentId);

        if ($segment->seller_id !== null) {
            return response()->json(['error' => 'This is not an admin segment.'], 403);
        }

        // Decide which base model to query
        if ($segment->segment_type === 'subscriber') {
            $entities = Subscriber::all();
        } else {
            $entities = User::with('order')->get();
        }

        $matched = $entities->filter(fn($entity) =>
            $this->evaluateRules($entity, json_decode($segment->rules, true))
        );

        // Response key depends on type (for convenience)
        $resultKey = $segment->segment_type === 'subscriber'
            ? 'matched_subscribers'
            : 'matched_users';

        $response = [
            'segment' => $segment,
            'count'   => $matched->count(),
        ];
        $response[$resultKey] = $matched->values();

        return response()->json($response);
    }

    /**
     * Evaluate rules against a single entity (User or Subscriber).
     * Compatible with AND/OR logic.
     *
     * $rulesGroup = [
     *   [ ['field' => 'email', 'operator' => 'contains', 'value' => '@gmail.com'], ... ],  // group1 (AND inside, OR between groups)
     *   [ ... ] // group2
     * ]
     */
    private function evaluateRules($entity, $rulesGroup)
    {
        if (empty($rulesGroup)) {
            return false;
        }

        foreach ($rulesGroup as $group) {
            $matchAll = true;

            foreach ($group as $rule) {
                $field    = $rule['field'];
                $operator = $rule['operator'];
                $value    = $rule['value'];

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
        // normalize for string-based operators
        if (in_array($operator, ['contains', 'starts_with', 'ends_with'])) {
            $actual   = (string) $actual;
            $expected = (string) $expected;
        }

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
        Segment::find($segment_id)?->delete();

        return response()->json(['message' => 'Segment deleted successfully.']);
    }

    public function multi_delete(Request $request)
    {
        Segment::whereIn('id', $request->ids)->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Segments Deleted Successfully!',
        ], 200);
    }
}
