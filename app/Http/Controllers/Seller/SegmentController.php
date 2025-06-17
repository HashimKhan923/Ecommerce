<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Segment;
use App\Models\MyCustomer;


class SegmentController extends Controller
{

    public function index($seller_id)
    {
      $data = Segment::where('seller_id',$seller_id)->get();

      return response()->json(['$data'=>$data]);

    }

    public function create(Request $request)
    {
        Segment::create([
        'seller_id'=>$seller_id,   
        'name' => $request->name,
        'rules' => $request->rules,
        ]);

        return response()->json(['message' => 'Segment created successfully.']);

    }

    public function update(Request $request)
    {
        Segment::where('id',$request->segment_id)->update([   
        'name' => $request->name,
        'rules' => $request->rules,
        ]);

        return response()->json(['message' => 'Segment updated successfully.']);

    }



    public function apply($segment_id)
    {
        $segment = Segment::findOrFail($segment_id);

        $customers = MyCustomer::with('customer', 'orders')
            ->where('seller_id', $segment->seller_id)
            ->get();

        $matchedCustomers = $customers->filter(function ($customer) use ($segment) {
            return $this->evaluateRules($customer, $segment->rules);
        });

        // $customerIds = $matchedCustomers->pluck('id')->toArray();

        // $segment->customers()->sync($customerIds);

        return response()->json([
            'message' => 'Segment applied successfully.',
            // 'matched_customer_count' => count($customerIds),
            'matchedCustomers' => $matchedCustomers,
        ]);
    }


    private function evaluateRules($customer, $rulesGroup)
    {
        $matchType = $rulesGroup['match_type'] ?? 'AND';
        $rules = $rulesGroup['rules'] ?? [];

        $results = [];

        foreach ($rules as $rule) {
            $relation = $rule['relation'] ?? null;
            $field = $rule['field'] ?? null;
            $aggregate = $rule['aggregate'] ?? null;
            $operator = $rule['operator'] ?? '=';
            $value = $rule['value'] ?? null;

            if (is_null($field)) {
                $results[] = false;
                continue;
            }

            // Case 1: No relation, direct customer field
            if (is_null($relation)) {
                $result = data_get($customer, $field);
            } else {
                if (!method_exists($customer, $relation)) {
                    $results[] = false;
                    continue;
                }

                $relationQuery = $customer->$relation();

                switch ($aggregate) {
                    case 'sum': $result = $relationQuery->sum($field); break;
                    case 'count': $result = $relationQuery->count(); break;
                    case 'avg': $result = $relationQuery->avg($field); break;
                    case 'min': $result = $relationQuery->min($field); break;
                    case 'max': $result = $relationQuery->max($field); break;
                    case 'first':
                        $related = $relationQuery->orderBy('id')->first();
                        $result = $related ? data_get($related, $field) : null;
                        break;
                    case 'last':
                        $related = $relationQuery->orderByDesc('id')->first();
                        $result = $related ? data_get($related, $field) : null;
                        break;
                    case 'exists': $result = $relationQuery->exists(); break;
                    case 'distinct_count': $result = $relationQuery->distinct($field)->count($field); break;
                    default:
                        $results[] = false;
                        continue;
                }
            }

            $results[] = $this->compare($result, $operator, $value);
        }

        return $matchType === 'AND'
            ? !in_array(false, $results, true)
            : in_array(true, $results, true);
    }


    private function compare($left, $operator, $right)
    {
        return match ($operator) {
            '>' => $left > $right,
            '>=' => $left >= $right,
            '<' => $left < $right,
            '<=' => $left <= $right,
            '=' => $left == $right,
            '!=' => $left != $right,
            default => false,
        };
    }

    public function delete($segment_id)
    {
        Segment::find($segment_id)->delete();

        return response()->json(['message' => 'Segment deleted successfully.']);

    }


}
