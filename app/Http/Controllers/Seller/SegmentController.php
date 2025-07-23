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
        $segments = Segment::where('seller_id', $seller_id)->get();

        $totalCustomerCount = MyCustomer::where('seller_id', $seller_id)->count();

        $data = $segments->map(function ($segment) use ($totalCustomerCount) {
            $customers = MyCustomer::with('customer', 'orders')
                ->where('seller_id', $segment->seller_id)
                ->get();

            $matchedCustomers = $customers->filter(function ($customer) use ($segment) {
                return $this->evaluateRules($customer, $segment->rules);
            });



            

            $matchedCount = $matchedCustomers->count();
            $percentage = $totalCustomerCount > 0
                ? round(($matchedCount / $totalCustomerCount) * 100, 2)
                : 0;

            return [
                'id' => $segment->id,
                'name' => $segment->name,
                'percentage' => $percentage,
                'last_activity' => $segment->updated_at->toDateTimeString(),
                'matchedCount' => $matchedCount,
                'totalCustomerCount' => $totalCustomerCount
            ];
        });

        return response()->json(['data' => $data]);
    }


    public function create(Request $request)
    {
        Segment::create([
        'seller_id'=>$request->seller_id,   
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





        public function check($seller_id, $rules)
        {
            

            $customers = MyCustomer::with('customer', 'orders')
                ->where('seller_id', $seller_id)
                ->get();

            $matchedCustomers = $customers->filter(fn($customer) =>
                $this->evaluateRules($customer, $rules, true)
            );




            return response()->json([
                'matchedCustomers' => $matchedCustomers,
            ]);
        }


        public function apply($segment_id)
        {
            $segment = Segment::findOrFail($segment_id);
            
            $customers = MyCustomer::with('customer', 'orders')
                ->where('seller_id', $segment->seller_id)
                ->get();

            $matchedCustomers = $customers->filter(fn($customer) =>
                $this->evaluateRules($customer, $segment->rules, true)
            );




            return response()->json([
                'message' => 'Segment applied successfully.',
                'segment' => $segment,
                'matchedCustomers' => $matchedCustomers,
            ]);
        }




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


        private function compare($left, $operator, $right)
        {
            return match ($operator) {
                '='  => $left == $right,
                '!=' => $left != $right,
                '>'  => $left > $right,
                '>=' => $left >= $right,
                '<'  => $left < $right,
                '<=' => $left <= $right,
                'LIKE' => str_contains($left, $right),
                'NOT LIKE' => !str_contains($left, $right),
                default => false,
            };
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
