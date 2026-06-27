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
    if (isset($savedVal['_column_widths']) && is_string($savedVal['_column_widths'])) {
        $savedVal['_column_widths'] = json_decode($savedVal['_column_widths'], true);
    }
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

    // Resolve dynamic variable map from context if available
    $applicationInstance = $application ?? $data ?? null;
    if (!$applicationInstance && isset($detail)) {
        $applicationInstance = $detail;
    }
    $dynamicVarMap = [];
    if ($applicationInstance instanceof \App\Models\DataPerijinan) {
        $dynamicVarMap = \App\Services\DocumentGenerator::getDynamicVariableMap($applicationInstance);
    }
@endphp

@if($originalRowCount > 0)
    <div class="overflow-x-auto mt-1 rounded-xl border border-gray-200 dark:border-gray-700 relative">
        <input type="hidden" name="{{ $prefix }}[_column_widths]" id="col_widths_{{ $prefix }}" value="{{ json_encode($savedVal['_column_widths'] ?? []) }}">
        <table class="w-full border-collapse text-sm text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-900 table-resizable" data-prefix="{{ $prefix }}" data-readonly="{{ empty($ro) ? 'false' : 'true' }}" data-original-rows="{{ $originalRowCount }}" style="table-layout: auto;">
            @foreach($rows as $rKey => $row)
                <tr>
                    @foreach($row as $cKey => $cell)
                        @php
                            $colspan = $cell['colspan'] ?? 1;
                            $rowspan = $cell['rowspan'] ?? 1;
                            $isInput = $cell['is_input'] ?? false;
                            $inputType = $cell['input_type'] ?? 'text';
                            $inputName = $cell['input_name'] ?? '';
                            $cellContent = $cell['content'] ?? '';
                            $savedCellVal = $savedVal[$inputName] ?? '';
                            $isDynamicVar = false;
                            $dynamicVarLabel = '';
                            if (!empty($cell['dynamic_var'])) {
                                $isDynamicVar = true;
                                $dynamicVarLabel = $cell['dynamic_var'];
                                $cleanVarName = strtolower(str_replace(['$', '{', '}', ' '], ['', '', '', '_'], $cell['dynamic_var']));
                                if (isset($dynamicVarMap[$cleanVarName])) {
                                    $savedCellVal = $dynamicVarMap[$cleanVarName];
                                }
                            }
                            $fmt = $cell['fmt'] ?? [];

                            // Retrieve saved column width if present
                            $savedWidth = $savedVal['_column_widths'][$cKey] ?? null;

                            // Construct cell inline styling based on builder configuration
                            $cellStyle = 'border: 1px solid #d1d5db; padding: 8px 10px; position: relative;';
                            if (!empty($savedWidth)) {
                                $cellStyle .= 'width: ' . $savedWidth . ';';
                            } elseif (!empty($fmt['width'])) {
                                $widthVal = preg_match('/^[0-9]+$/', trim($fmt['width'])) ? trim($fmt['width']) . '%' : trim($fmt['width']);
                                $cellStyle .= 'width: ' . $widthVal . ';';
                            } else {
                                $cellStyle .= 'min-width: 60px;';
                            }
                            if (!empty($fmt['fontFamily'])) {
                                $cellStyle .= 'font-family: ' . $fmt['fontFamily'] . ';';
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
                        
                        <td colspan="{{ $colspan }}" rowspan="{{ $rowspan }}" style="{{ $cellStyle }}" class="border border-gray-300 dark:border-gray-600 p-2 relative">
                            @if(empty($ro) && $rKey === 0)
                                <div class="col-resize-handle hover:bg-indigo-500/30 transition-colors" data-col-index="{{ $cKey }}" style="position: absolute; right: 0; top: 0; bottom: 0; width: 6px; cursor: col-resize; z-index: 10; user-select: none;"></div>
                            @endif
                            @if($isInput)
                                @php
                                    $cellAttr = $ro;
                                    if ($isDynamicVar) {
                                        $cellAttr .= ' readonly';
                                    }
                                @endphp
                                @if($inputType === 'date')
                                    <input type="date"
                                        name="{{ $prefix }}[{{ $inputName }}]"
                                        value="{{ $savedCellVal }}"
                                        {{ $cellAttr }}
                                        class="w-full px-2 py-1 text-xs border border-gray-200 dark:border-gray-700 bg-transparent focus:ring-1 focus:ring-indigo-400 outline-none rounded text-gray-900 dark:text-gray-100 {{ $isDynamicVar ? 'bg-gray-100/50 dark:bg-gray-800/50 cursor-not-allowed text-gray-500' : '' }}"
                                        style="text-align: inherit; font-size: inherit; font-weight: inherit; color: inherit; background: transparent;">
                                @elseif($inputType === 'number')
                                    <input type="number"
                                        name="{{ $prefix }}[{{ $inputName }}]"
                                        value="{{ $savedCellVal }}"
                                        {{ $cellAttr }}
                                        class="w-full px-2 py-1 text-xs border border-gray-200 dark:border-gray-700 bg-transparent focus:ring-1 focus:ring-indigo-400 outline-none rounded text-gray-900 dark:text-gray-100 {{ $isDynamicVar ? 'bg-gray-100/50 dark:bg-gray-800/50 cursor-not-allowed text-gray-500' : '' }}"
                                        style="text-align: inherit; font-size: inherit; font-weight: inherit; color: inherit; background: transparent;">
                                @else
                                    <textarea
                                        name="{{ $prefix }}[{{ $inputName }}]"
                                        {{ $cellAttr }}
                                        rows="1"
                                        class="w-full px-2 py-1 text-xs border border-gray-200 dark:border-gray-700 bg-transparent focus:ring-1 focus:ring-indigo-400 outline-none rounded text-gray-900 dark:text-gray-100 {{ $isDynamicVar ? 'bg-gray-100/50 dark:bg-gray-800/50 cursor-not-allowed text-gray-500' : '' }}"
                                        style="text-align: inherit; font-size: inherit; font-weight: inherit; color: inherit; background: transparent; min-height: 24px; display: block; resize: {{ $isDynamicVar ? 'none' : 'vertical' }};"
                                        oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px';"
                                    >{{ $savedCellVal }}</textarea>
                                @endif

                                @if($isDynamicVar)
                                    <div class="text-[9px] text-indigo-600 dark:text-indigo-400 mt-1 italic text-center leading-tight">
                                        <span class="block text-[8px] opacity-75">Abaikan saja, terisi otomatis dari:</span>
                                        <strong class="font-bold font-mono">{{ $dynamicVarLabel }}</strong>
                                    </div>
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
            function initResizableTables() {
                document.querySelectorAll('.table-resizable').forEach(table => {
                    if (table.dataset.resizableInitialized === 'true') return;
                    table.dataset.resizableInitialized = 'true';

                    const tablePrefix = table.dataset.prefix;
                    const hiddenInput = document.getElementById('col_widths_' + tablePrefix);
                    if (!hiddenInput) return;

                    const firstRow = table.querySelector('tr:first-child');
                    if (!firstRow) return;

                    const handles = firstRow.querySelectorAll('.col-resize-handle');
                    handles.forEach(handle => {
                        const colIndex = parseInt(handle.dataset.colIndex);
                        const cell = handle.closest('td, th');
                        
                        let startX, startWidth;

                        handle.addEventListener('mousedown', e => {
                            e.preventDefault();
                            startX = e.pageX;
                            startWidth = cell.offsetWidth;

                            handle.style.backgroundColor = 'rgba(99, 102, 241, 0.4)'; // Highlight on drag

                            const onMouseMove = ev => {
                                const newWidth = Math.max(30, startWidth + (ev.pageX - startX));
                                cell.style.width = newWidth + 'px';
                                
                                let widths = {};
                                try {
                                    widths = JSON.parse(hiddenInput.value || '{}');
                                } catch(err) {}
                                widths[colIndex] = newWidth + 'px';
                                hiddenInput.value = JSON.stringify(widths);
                            };

                            const onMouseUp = () => {
                                handle.style.backgroundColor = '';
                                document.removeEventListener('mousemove', onMouseMove);
                                document.removeEventListener('mouseup', onMouseUp);
                            };

                            document.addEventListener('mousemove', onMouseMove);
                            document.addEventListener('mouseup', onMouseUp);
                        });
                    });
                });
            }

            // Trigger immediately and register listeners
            initResizableTables();
            document.addEventListener('DOMContentLoaded', initResizableTables);
            window.addEventListener('load', initResizableTables);

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
