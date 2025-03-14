<!-- <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css" /> -->
<table id="{{ $id }}" class="table">
    <thead>
        <tr>
            @foreach ($columns as $column)
            <th>
                @if (method_exists($model, 'attributeLabels'))
                {{ $model->attributeLabels($column) }}
                @else
                @if (is_array($column))
                @if (isset($column['label']))
                {{ $column['label'] }}
                @else
                {{ ucwords(str_replace('_', ' ', $column['attribute'])) }}
                @endif
                @else
                {{ ucwords(str_replace('_', ' ', $column)) }}
                @endif
                @endif
            </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>


<script>
    (function($) {
        'use strict';
        // Ensure $id is valid before using it in a jQuery selector
        var tableId = '{{ $id }}';
        var searching = '{{ $searching }}';
        var paging = '{{ $paging }}';
        var info = '{{ $info }}';
        if (tableId) { // Added check to ensure tableId is not empty
            var table = $('#' + tableId).DataTable({
                order: [],
                lengthMenu: [
                    [10, 25, 50, 100, 500],
                    [10, 25, 50, 100, 500]
                ],
                processing: true,
                serverSide: true,
                autoWidth: false,
                searching: searching, // Disable search
                info: info, // Disable search
                paging: paging,
                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                language: {
                    processing: '<div class="spinner-border" style="width: 50px; height: 50px;" role="status"><span class="visually-hidden">Loading...</span></div>'
                },
                ajax: {
                    url: "{{ $url }}",
                    type: "GET",
                    data: function(d) {
                        @foreach ($customfilterIds as $customfilterId)
                            var filterId = '{{ $customfilterId }}';
                            if (filterId && $('#' + filterId)
                                .length) { // Check if filterId element exists
                                d[filterId] = $('#' + filterId).val();
                            }
                        @endforeach
                    },
                    error: function(xhr, error, thrown) {
                        console.log('Error details:', xhr.responseText);
                        alert(
                            'An error occurred while loading the data. Please check the console for more details.'
                        );
                    }
                },
                columns: [
                    @foreach ($columns as $column)
                        {
                            @if (is_array($column))
                                data: '{{ $column['attribute'] ?? '' }}',
                                name: '{{ $column['attribute'] ?? '' }}'
                            @else
                                data: '{{ $column }}',
                                name: '{{ $column }}'
                            @endif
                        },
                    @endforeach
                ],
                buttons: [
                    @foreach ($buttons as $button)
                        @if (is_array($button))
                            {
                                @foreach ($button as $key => $value)
                                    '{{ $key }}': '{{ $value }}',
                                @endforeach
                            },
                        @else
                            '{{ $button }}',
                        @endif
                    @endforeach
                ]
            });


            // Ensure $filterButtonId is valid before using it in a jQuery selector
            var filterButtonId = '{{ $filterButtonId }}';
            if (filterButtonId) { // Added check to ensure filterButtonId is not empty
                $('#' + filterButtonId).on('click', function() {
                    table.ajax.reload();
                });
            }
        }


        function tableReload() {
            table.ajax.reload();
        }
        var cart_table_reload = 'cart_table_reload';
        if (cart_table_reload) { // Added check to ensure cart_table_reload is not empty
            $('#' + cart_table_reload).on('click', function() {
                tableReload()
            });
        }


    })(jQuery);
</script>