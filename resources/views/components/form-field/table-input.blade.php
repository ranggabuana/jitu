{{--
    Reusable table field renderer for validator & applicant forms.
    Props:
      - $field            : PerijinanFormField instance (type === 'table')
      - $val              : Current saved values (array keyed by input_name, or null)
      - $ro              : 'readonly disabled' string if field is read-only, else ''
      - $inputNamePrefix  : Custom prefix for input names (defaults to $field->name)
--}}
@php
    $rawTableData = $field->options['table_data'] ?? null;
    // options is cast to array, so table_data may be a JSON string that needs decoding
    if (is_string($rawTableData)) {
        $tableData = json_decode($rawTableData, true);
    } else {
        $tableData = $rawTableData;
    }
    $rows = $tableData['rows'] ?? [];
    $savedVal = is_array($val) ? $val : [];
    $prefix = $inputNamePrefix ?? $field->name;
@endphp

@if(count($rows) > 0)
    <div class="overflow-x-auto mt-1 rounded-xl border border-gray-200 dark:border-gray-700">
        <table class="w-full border-collapse text-sm text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-900" style="table-layout: auto;">
            @foreach($rows as $row)
                <tr>
                    @foreach($row as $cell)
                        @php
                            $colspan = $cell['colspan'] ?? 1;
                            $rowspan = $cell['rowspan'] ?? 1;
                            $isInput = $cell['is_input'] ?? false;
                            $inputType = $cell['input_type'] ?? 'text';
                            $inputName = $cell['input_name'] ?? '';
                            $cellContent = $cell['content'] ?? '';
                            $savedCellVal = $savedVal[$inputName] ?? '';
                            $fmt = $cell['fmt'] ?? [];

                            // Construct cell inline styling based on builder configuration
                            $cellStyle = 'border: 1px solid #d1d5db; padding: 8px 10px; min-width: 60px;';
                            if (!empty($fmt['fontFamily'])) {
                                $cellStyle .= 'font-family: ' . $fmt['fontFamily'] . ';';
                            }
                            if (!empty($fmt['width'])) {
                                $cellStyle .= 'width: ' . $fmt['width'] . '%;';
                            }
                            if (!empty($fmt['bgColor']) && $fmt['bgColor'] !== '#ffffff') {
                                $cellStyle .= 'background-color: ' . $fmt['bgColor'] . ';';
                            }
                            if (!empty($fmt['color']) && $fmt['color'] !== '#000000') {
                                $cellStyle .= 'color: ' . $fmt['color'] . ';';
                            }
                            if (!empty($fmt['fontSize'])) {
                                $cellStyle .= 'font-size: ' . $fmt['fontSize'] . ';';
                            }
                            if (!empty($fmt['bold'])) {
                                $cellStyle .= 'font-weight: 700;';
                            } else {
                                $cellStyle .= 'font-weight: 600;';
                            }
                            if (!empty($fmt['italic'])) {
                                $cellStyle .= 'font-style: italic;';
                            }
                            if (!empty($fmt['underline'])) {
                                $cellStyle .= 'text-decoration: underline;';
                            }
                            $align = $fmt['align'] ?? 'center';
                            $cellStyle .= 'text-align: ' . $align . ';';
                        @endphp
                        
                        <td colspan="{{ $colspan }}" rowspan="{{ $rowspan }}" style="{{ $cellStyle }}" class="border border-gray-300 dark:border-gray-600 p-2">
                            @if($isInput)
                                @if($inputType === 'date')
                                    <input type="date"
                                        name="{{ $prefix }}[{{ $inputName }}]"
                                        value="{{ $savedCellVal }}"
                                        {{ $ro }}
                                        class="w-full px-2 py-1 text-xs border-0 bg-transparent focus:ring-1 focus:ring-indigo-400 outline-none rounded text-gray-900 dark:text-gray-100"
                                        style="text-align: inherit; font-size: inherit; font-weight: inherit; color: inherit; background: transparent;">
                                @elseif($inputType === 'number')
                                    <input type="number"
                                        name="{{ $prefix }}[{{ $inputName }}]"
                                        value="{{ $savedCellVal }}"
                                        {{ $ro }}
                                        class="w-full px-2 py-1 text-xs border-0 bg-transparent focus:ring-1 focus:ring-indigo-400 outline-none rounded text-gray-900 dark:text-gray-100"
                                        style="text-align: inherit; font-size: inherit; font-weight: inherit; color: inherit; background: transparent;">
                                @else
                                    <input type="text"
                                        name="{{ $prefix }}[{{ $inputName }}]"
                                        value="{{ $savedCellVal }}"
                                        {{ $ro }}
                                        class="w-full px-2 py-1 text-xs border-0 bg-transparent focus:ring-1 focus:ring-indigo-400 outline-none rounded text-gray-900 dark:text-gray-100"
                                        style="text-align: inherit; font-size: inherit; font-weight: inherit; color: inherit; background: transparent;">
                                @endif
                            @else
                                {{ $cellContent ?: '-' }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </table>
    </div>
@else
    <p class="text-xs text-gray-400 italic mt-1">Desain table belum dikonfigurasi.</p>
@endif
