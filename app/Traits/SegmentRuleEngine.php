<?php

namespace App\Traits;

trait SegmentRuleEngine
{
    private function evaluateRules($customer, $rulesGroup)
    {
        $matchType = $rulesGroup['match_type'] ?? 'AND';
        $rules = $rulesGroup['rules'] ?? [];

        if (empty($rules)) return true;

        $results = [];

        foreach ($rules as $rule) {
            $field = $rule['field'];
            $operator = $rule['operator'];
            $value = $rule['value'];

            $parts = explode('.', $field);

            if (count($parts) === 2) {
                [$relation, $fieldPart] = $parts;

                $aggregates = ['count','sum','avg','min','max','first','last','distinct_count'];

                if (in_array($fieldPart, $aggregates)) {
                    $query = $customer->$relation();

                    switch ($fieldPart) {
                        case 'count': $actual = $query->count(); break;
                        case 'sum': $actual = $query->sum($rule['sum_field'] ?? 'amount'); break;
                        case 'avg': $actual = $query->avg($rule['avg_field'] ?? 'amount'); break;
                        case 'min': $actual = $query->min($rule['min_field'] ?? 'amount'); break;
                        case 'max': $actual = $query->max($rule['max_field'] ?? 'amount'); break;
                        case 'first':
                            $rec = $query->orderBy('id')->first();
                            $actual = $rec ? data_get($rec, $rule['first_field']) : null;
                            break;
                        case 'last':
                            $rec = $query->orderByDesc('id')->first();
                            $actual = $rec ? data_get($rec, $rule['last_field']) : null;
                            break;
                        case 'distinct_count':
                            $actual = $query->distinct($rule['distinct_field'])->count($rule['distinct_field']);
                            break;
                    }

                    $results[] = $this->compare($actual, $operator, $value);
                } else {
                    $related = $customer->$relation;

                    if ($related instanceof \Illuminate\Support\Collection) {
                        $results[] = $related->contains(function ($item) use ($fieldPart, $operator, $value) {
                            return $this->compare(data_get($item, $fieldPart), $operator, $value);
                        });
                    } else {
                        $actual = data_get($related, $fieldPart);
                        $results[] = $this->compare($actual, $operator, $value);
                    }
                }
            } else {
                $actual = data_get($customer, $field);
                $results[] = $this->compare($actual, $operator, $value);
            }
        }

        return $matchType === 'AND'
            ? !in_array(false, $results, true)
            : in_array(true, $results, true);
    }

    private function compare($actual, $operator, $expected)
    {
        if (in_array($operator, ['contains','starts_with','ends_with'])) {
            $actual = (string) $actual;
            $expected = (string) $expected;
        }

        return match ($operator) {
            '=' => $actual == $expected,
            '!=' => $actual != $expected,
            '>' => $actual > $expected,
            '<' => $actual < $expected,
            '>=' => $actual >= $expected,
            '<=' => $actual <= $expected,
            'contains' => stripos($actual, $expected) !== false,
            'starts_with' => stripos($actual, $expected) === 0,
            'ends_with' => str_ends_with(strtolower($actual), strtolower($expected)),
            default => false
        };
    }
}
