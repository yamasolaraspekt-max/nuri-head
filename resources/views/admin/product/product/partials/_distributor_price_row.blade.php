<tr>
    <td>{{ $row->article_no ?: '–' }}</td>
    <td>
        <a href="{{ url('distributor_price/'.$row->distributor_id.'/'.request()->id) }}">
            {{ $row->distributor_name }}
        </a>
    </td>
    <td>{{ $row->price !== null ? number_format($row->price, 2, ',', '.') . ' €' : '–' }}</td>
    <td>{{ $row->discount_price !== null ? number_format($row->discount_price, 2, ',', '.') . ' €' : '–' }}</td>
    <td>{{ $row->discount_percent !== null ? $row->discount_percent.'%' : '–' }}</td>
    <td>{{ $row->purchase_price !== null ? number_format($row->purchase_price, 2, ',', '.') . ' €' : '–' }}</td>
    <td>{{ $row->price_date ?: '–' }}</td>
    <td>{{ $row->availability ?: '–' }}</td>
</tr>
