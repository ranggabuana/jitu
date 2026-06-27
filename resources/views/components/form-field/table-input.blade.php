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

    // Dynamically expand rows if savedVal contains keys beyond original rows
    $maxRowIndex = count($rows) - 1;
    foreach ($savedVal as $key => $v) {
        if (preg_match('/cell_(\d+)_/i', $key, $matches)) {
            $rIndex = intval($matches[1]);
            if ($rIndex > $maxRowIndex) {
                $maxRowIndex = $rIndex;
            }
        }
    }
    $originalRowCount = count($rows);
    if ($originalRowCount > 0 && $maxRowIndex >= $originalRowCount) {
        $lastRowTemplate = $rows[$originalRowCount - 1];
        for ($r = $originalRowCount; $r <= $maxRowIndex; $r++) {
            $newRow = [];
            $isFirstCell = true;
            foreach ($lastRowTemplate as $cell) {
                $newCell = $cell;
                if (!empty($cell['input_name'])) {
                    $newCell['input_name'] = preg_replace('/cell_\d+_/', 'cell_' . $r . '_', $cell['input_name']);
                }
                if ($isFirstCell && empty($cell['is_input']) && isset($cell['content'])) {
                    $content = trim($cell['content']);
                    if (ctype_digit($content)) {
                        $diff = $r - ($originalRowCount - 1);
                        $newCell['content'] = strval(intval($content) + $diff);
                    }
                }
                $isFirstCell = false;
                $newRow[] = $newCell;
            }
            $rows[$r] = $newRow;
        }
    }
@endphp

@if($originalRowCount > 0)
    <div class="overflow-x-auto mt-1 rounded-xl border border-gray-200 dark:border-gray-700">
        <table class="w-full border-collapse text-sm text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-900" data-original-rows="{{ $originalRowCount }}" style="table-layout: auto;">
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
                                    <textarea
                                        name="{{ $prefix }}[{{ $inputName }}]"
                                        {{ $ro }}
                                        rows="1"
                                        class="w-full px-2 py-1 text-xs border-0 bg-transparent focus:ring-1 focus:ring-indigo-400 outline-none rounded text-gray-900 dark:text-gray-100"
                                        style="text-align: inherit; font-size: inherit; font-weight: inherit; color: inherit; background: transparent; min-height: 24px; display: block; resize: vertical;"
                                        oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px';"
                                    >{{ $savedCellVal }}</textarea>
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
    
    @if(empty($ro))
        <div class="mt-2 flex gap-2 justify-start">
            <button type="button" onclick="addTableRow('{{ $prefix }}', this)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 rounded-lg transition-colors border border-indigo-200 dark:border-indigo-800">
                <i class="fas fa-plus"></i> Tambah Baris
            </button>
            <button type="button" onclick="removeLastTableRow('{{ $prefix }}', this)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 rounded-lg transition-colors border border-red-200 dark:border-red-800">
                <i class="fas fa-minus"></i> Hapus Baris
            </button>
        </div>
    @endif

    <script>
        (function() {
            function adjustHeights() {
                document.querySelectorAll('textarea[name^="{{ $prefix }}"]').forEach(el => {
                    el.style.height = 'auto';
                    el.style.height = (el.scrollHeight > 24 ? el.scrollHeight : 24) + 'px';
                });
            }
            adjustHeights();
            window.addEventListener('load', adjustHeights);
            document.addEventListener('DOMContentLoaded', adjustHeights);

            if (typeof window.addTableRow !== 'function') {
                window.addTableRow = function(prefix, buttonEl) {
                    const container = buttonEl.closest('.overflow-x-auto') || buttonEl.parentElement.previousElementSibling;
                    const table = container.querySelector('table');
                    if (!table) return;

                    const tbody = table.querySelector('tbody') || table;
                    const rows = tbody.querySelectorAll('tr');
                    if (rows.length === 0) return;

                    const lastRow = rows[rows.length - 1];
                    const clonedRow = lastRow.cloneNode(true);

                    // Find last row index from input names in the last row
                    let lastRowIndex = 0;
                    const inputs = lastRow.querySelectorAll('input, textarea');
                    if (inputs.length > 0) {
                        const match = inputs[0].name.match(/cell_(\d+)_/);
                        if (match) {
                            lastRowIndex = parseInt(match[1]);
                        }
                    }
                    const nextRowIndex = lastRowIndex + 1;

                    // Update inputs in the cloned row
                    clonedRow.querySelectorAll('input, textarea').forEach(el => {
                        el.name = el.name.replace(`cell_${lastRowIndex}_`, `cell_${nextRowIndex}_`);
                        el.value = '';
                        if (el.tagName.toLowerCase() === 'textarea') {
                            el.style.height = '24px';
                            el.addEventListener('input', function() {
                                this.style.height = '';
                                this.style.height = this.scrollHeight + 'px';
                            });
                        }
                    });

                    // Increment first cell if it contains a number sequence
                    const firstCell = clonedRow.querySelector('td');
                    if (firstCell && !firstCell.querySelector('input, textarea')) {
                        const text = firstCell.textContent.trim();
                        if (/^\d+$/.test(text)) {
                            firstCell.textContent = parseInt(text) + 1;
                        }
                    }

                    tbody.appendChild(clonedRow);
                };
            }

            if (typeof window.removeLastTableRow !== 'function') {
                window.removeLastTableRow = function(prefix, buttonEl) {
                    const container = buttonEl.closest('.overflow-x-auto') || buttonEl.parentElement.previousElementSibling;
                    const table = container.querySelector('table');
                    if (!table) return;

                    const tbody = table.querySelector('tbody') || table;
                    const rows = tbody.querySelectorAll('tr');
                    const originalRows = parseInt(table.getAttribute('data-original-rows')) || 0;

                    if (rows.length > originalRows) {
                        rows[rows.length - 1].remove();
                    } else {
                        alert('Tidak dapat menghapus baris bawaan template.');
                    }
                };
            }
        })();
    </script>
@else
    <p class="text-xs text-gray-400 italic mt-1">Desain table belum dikonfigurasi.</p>
@endif
