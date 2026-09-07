@php
    $fmt = fn (float $number): string => number_format($number, 2);
    $pass = (int) $passingMark;
    $gradesAscending = ['3.00', '2.75', '2.50', '2.25', '2.00', '1.75', '1.50', '1.25', '1.00'];
    $span = 100 - $pass;

    // Compute shared boundaries first so adjacent bands have neither gaps nor overlaps.
    $lowerBounds = collect(range(0, 8))
        ->map(fn (int $index): float => round($pass + ($span * $index / 9), 2));

    $bandsAscending = collect($gradesAscending)->map(function (string $grade, int $index) use ($lowerBounds): array {
        $lower = $lowerBounds[$index];
        $upper = $index === 8 ? 100.00 : round($lowerBounds[$index + 1] - 0.01, 2);

        return ['grade' => $grade, 'lo' => $lower, 'hi' => $upper];
    });

    $entries = $bandsAscending
        ->reverse()
        ->values()
        ->map(fn (array $band): array => [
            'grade' => $band['grade'],
            'range' => $fmt($band['lo']) . ' – ' . $fmt($band['hi']),
        ])
        ->push(['grade' => '5.00', 'range' => 'Below ' . $pass]);

    // Ten entries fit cleanly into two score/grade pairs with five rows.
    $columnCount = max(1, (int) ceil($entries->count() / 5));
    $rowCount = (int) ceil($entries->count() / $columnCount);
    $columns = $entries
        ->chunk($rowCount)
        ->map(fn ($column) => $column->values())
        ->values();
@endphp

<div class="table-indent">
    <table>
        <thead>
            <tr>
                @foreach ($columns as $column)
                    <th style="text-align:center;">Average Score</th>
                    <th style="text-align:center;">Grade</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @for ($row = 0; $row < $rowCount; $row++)
                <tr>
                    @foreach ($columns as $column)
                        @php($entry = $column->get($row))
                        <td style="text-align:center;">{{ $entry['range'] ?? '' }}</td>
                        <td style="text-align:center;">{{ $entry['grade'] ?? '' }}</td>
                    @endforeach
                </tr>
            @endfor
        </tbody>
    </table>
    <p style="margin-top:4px;" class="indent-level-1">Passing Mark: {{ $pass }}%</p>
</div>
