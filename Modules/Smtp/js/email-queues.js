(function ($) {
    'use strict';


    $(document).ready(function () {
        var searchable = [];
        var selectable = [];


        var dTable = $('#email_queues').DataTable({
            //console.log("testing",name);

            order: [],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            processing: true,
            responsive: false,
            serverSide: true,
            processing: true,
            bAutoWidth: false, // Disable the auto width calculation 
            aoColumns: [
                { "sWidth": "30%" }, // 1st column width 
                { "sWidth": "30%" }, // 2nd column width 
                { "sWidth": "40%" }, // 3rd column width and so on 
                { "sWidth": null } // 3rd column width and so on 
            ],
            language: {
                processing: ' <div class="spinner-border" style="width: 50px; height: 50px;" role="status"><span class="visually-hidden">Loading...</span></div>'
            },
            scroller: {
                loadingIndicator: false
            },
            pagingType: "full_numbers",
            dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
            ajax: {
                url: base_url + 'email-queues/get-list',
                type: "get"
            },

            columns: [
                // {
                //     data: null,

                //     render: function (data, type, row) {

                //         var name = data.full_name + ' S/O ' + data.father_name;
                //         name = data.customerClickAble ? '<a href="' + base_url + 'customer/edit/' + data.id + '"  class="text-decoration-none">' + name + '</a>' : name;

                //         return name;
                //     },

                //     name: 'full_name'
                // },
                //{data:'full_name', name: 'full_name', orderable: false, searchable: false},

                // {data:'full_name', name: 'full_name'},

                // d_o_b With time 
                // {data:'d_o_b', name: 'd_o_b'},

                // d_o_b without time 
                // {data:'d_o_b', name: 'd_o_b',render:function(data){return data!=null ? data.substr(0,10) : '';}},
                { data: 'id', name: 'id' },
                { data: 'subject', name: 'subject' },
                { data: 'from', name: 'from'},
                { data: 'to', name: 'to' },
                { data: 'status', name: 'status', render: function (data) { return data != null ? '<span class="badge badge-'+data+'">' + data + '</span>' : ''; } },
                { data: 'created_at', name: 'created_at', render: function (data) { return data != null ? data.substr(0, 10) : ''; } },
                { data: 'action', name: 'action' }

            ],
            buttons: [

                {
                    extend: 'excel',
                    className: 'btn-sm btn-dark',
                    title: 'List of Customer Details',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible',
                    }
                },

            ],
            initComplete: function () {
                var api = this.api();
                api.columns(searchable).every(function () {
                    var column = this;
                    var input = document.createElement("input");
                    input.setAttribute('placeholder', $(column.header()).text());
                    input.setAttribute('style', 'width: 140px; height:25px; border:1px solid whitesmoke;');

                    $(input).appendTo($(column.header()).empty())
                        .on('keyup', function () {
                            column.search($(this).val(), false, false, true).draw();
                        });

                    $('input', this.column(column).header()).on('click', function (e) {
                        e.stopPropagation();
                    });
                });

                api.columns(selectable).every(function (i, x) {
                    var column = this;

                    var select = $('<select style="width: 140px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">' + $(column.header()).text() + '</option></select>')
                        .appendTo($(column.header()).empty())
                        .on('change', function (e) {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                            e.stopPropagation();
                        });

                    $.each(dropdownList[i], function (j, v) {
                        select.append('<option value="' + v + '">' + v + '</option>')
                    });
                });
            }
        });
    });


})(jQuery);