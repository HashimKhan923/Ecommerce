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
    

    // Fetch only admin segments
    $segments = Segment::whereNull('seller_id')
        ->where('segment_type','user')->orWhere('segment_type','subscriber')
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

        // $rules = json_decode($segment->rules, true) ?? [];

        // Find matched based on rules
        $matched = $entities->filter(function ($entity) use ($segment) {
            return $this->evaluateRules($entity, $segment->rules);
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
    public function update(Request $request)
    {
        Segment::where('id',$request->segment_id)->update([   
        'name' => $request->name,
        'rules' => $request->rules,
        ]);

        return response()->json(['message' => 'Segment updated successfully.']);
    }

    /**
     * Apply segment rules and return matched entities (users or subscribers).
     */
    public function apply($segmentId)
    {
        $segment = Segment::findOrFail($segmentId);

        // Only admin segments (no seller_id)
        if ($segment->seller_id !== null) {
            return response()->json(['error' => 'This is not an admin segment.'], 403);
        }

        // Decide which base model to query
        if ($segment->segment_type === 'subscriber') {
            $entities = Subscriber::all();
        } else {
            $entities = User::with('order')->get();
        }

        $totalEntities = $entities->count();

        // Decode rules once
        // $rulesGroup = json_decode($segment->rules, true) ?? [];

        // Filter entities using your rules engine
        $matched = $entities->filter(function ($entity) use ($segment) {
            return $this->evaluateRules($entity, $segment->rules, true);
        });

        $matchedCount = $matched->count();

        $percentage = $totalEntities > 0
            ? round(($matchedCount / $totalEntities) * 100, 2)
            : 0;

        // Response key depends on type (for convenience)
        $resultKey = $segment->segment_type === 'subscriber'
            ? 'matched_subscribers'
            : 'matched_users';

        return response()->json([
            'segment'         => $segment,
            'total_entities'  => $totalEntities,
            'count'           => $matchedCount,
            'percentage'      => $percentage,
            $resultKey        => $matched->values(),
        ]);
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
private function evaluateRules($customer, $rulesGroup)
{
    $matchType = $rulesGroup['match_type'] ?? 'AND';
    $rules = $rulesGroup['rules'] ?? [];

    // If no rules, match all customers
    if (empty($rules)) {
        return true;
    }

    $results = [];

    foreach ($rules as $rule) {
        $field = $rule['field'];
        $operator = $rule['operator'];
        $value = $rule['value'];

        $parts = explode('.', $field);

        if (count($parts) === 2) {
            [$relation, $fieldPart] = $parts;

            $aggregates = ['count', 'sum', 'avg', 'min', 'max', 'first', 'last', 'distinct_count'];

            if (in_array($fieldPart, $aggregates)) {
                $query = $customer->$relation();

                switch ($fieldPart) {
                    case 'count':
                        $actual = $query->count();
                        break;
                    case 'sum':
                        $sumField = $rule['sum_field'] ?? 'amount';
                        $actual = $query->sum($sumField);
                        break;
                    case 'avg':
                        $avgField = $rule['avg_field'] ?? 'amount';
                        $actual = $query->avg($avgField);
                        break;
                    case 'min':
                        $minField = $rule['min_field'] ?? 'amount';
                        $actual = $query->min($minField);
                        break;
                    case 'max':
                        $maxField = $rule['max_field'] ?? 'amount';
                        $actual = $query->max($maxField);
                        break;
                    case 'first':
                        $firstField = $rule['first_field'] ?? null;
                        $record = $query->orderBy('id')->first();
                        $actual = $record && $firstField ? data_get($record, $firstField) : null;
                        break;
                    case 'last':
                        $lastField = $rule['last_field'] ?? null;
                        $record = $query->orderByDesc('id')->first();
                        $actual = $record && $lastField ? data_get($record, $lastField) : null;
                        break;
                    case 'distinct_count':
                        $distinctField = $rule['distinct_field'] ?? null;
                        $actual = $distinctField ? $query->distinct($distinctField)->count($distinctField) : null;
                        break;
                    default:
                        $actual = null;
                }

                $results[] = $this->compare($actual, $operator, $value);
            } else {
                $related = $customer->$relation;

                if ($related instanceof \Illuminate\Support\Collection) {
                    // MANY: match ANY related record
                    $matchAny = $related->contains(function ($item) use ($fieldPart, $operator, $value) {
                        return $this->compare(data_get($item, $fieldPart), $operator, $value);
                    });
                    $results[] = $matchAny;
                } else {
                    // Single related row
                    $actual = data_get($related, $fieldPart);
                    $results[] = $this->compare($actual, $operator, $value);
                }
            }
        } else {
            // Direct customer field
            $actual = data_get($customer, $field);
            $results[] = $this->compare($actual, $operator, $value);
        }
    }

    return $matchType === 'AND'
        ? !in_array(false, $results, true)
        : in_array(true, $results, true);
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
