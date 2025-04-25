<div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
    <script>
    var $jq = jQuery.noConflict();
    $jq(document).ready(function() {
        @foreach($column as $row)
            // Initialize the typeahead
            $jq('#{{ isset($row['id']) ? $row['id'] : '' }}').typeahead({
                minLength: '{{ isset($row['minLength']) ? $row['minLength'] : 1 }}',
                source: function(query, process) {
                    return $jq.get("{{ isset($row['url']) ? url($row['url']) : ''}}", { query: query }, function(data) {
            return process(data);
                    });
                },
                displayText: function(item) {
                    if (item.table_number) {
                        return 'Table-' + item.table_number + ' (' + item.seats + ' seats)';
                    } else if (item.name) {
                        return item.name + (item.contact_no ? ' - ' + item.contact_no : '');
                    } else {
                        return 'Unknown';
                    }
                },
                @if(isset($row['updater'])) 
                    updater: function(item) {
                        $jq('#{{ isset($row['updater']) ? $row['updater'] : '' }}').val(item.id);
                        return item;
                    }
                @endif
            });
        @endforeach
    });
</script>

</div>