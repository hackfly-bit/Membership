<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Update Transaksi</title>
    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 12px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            text-align: left;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .invoice-box.rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .invoice-box.rtl table {
            text-align: right;
        }

        .invoice-box.rtl table tr td:nth-child(2) {
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="heading">
                <td width="10%">Code</td>
                <td width="15%">Customer</td>
                <td width="10%">Nomor Hp</td>    
                <td width="10%">Tanggal</td>
                <td width="15%">Nominal</td>
                <td width=5%">Kategori</td>
                <td width="5%">Point</td>
                <td>Keterangan</td>
            </tr>
         
            {{-- total of nominal --}}
            @php
                $total = 0;
                $total_point = 0;
            @endphp

            @foreach ($transaksi as $x)
                @php
                    $total += $x->nominal;
                    $total_point += $x->point;
                @endphp
                <tr class="item @if ($loop->last) last @endif">
                    <td>{{ $x->code }}</td>
                    <td>{{ $x->customer->nama }}</td>
                    <td>{{ $x->customer->nohp }}</td>
                    <td>{{ $x->tanggal }}</td>
                    <td>Rp. {{ number_format($x->nominal, 0, ',', '.') }}</td>
                    <td>{{ $x->kategori->nama_kategory }}</td>
                    <td>{{ $x->point }}</td>
                    <td>{{ $x->keterangan }}</td>
                </tr>
            @endforeach

            <tr class="total">
                <td colspan="4"></td>
                <td>Rp. {{ number_format($total, 0, ',', '.') }}</td>
                <td></td>
                <td> Total Point {{ $total_point }} </td>
            </tr>
        </table>
    </div>

    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="heading">
                <td width="15%">Nama Customer</td>
                <td width="15%">Nomor HP</td>
                <td width="25%">Point WD</td>
                <td width="25%">Keterangan</td>    
                <td width="15%">Tanggal</td>
            </tr>
         
            {{-- total of nominal --}}
            @php
                $totalWD = 0;
            @endphp

            @foreach ($withdraw as $x)
                @php
                    $totalWD += $x->point;
                @endphp
                <tr class="item @if ($loop->last) last @endif">
                    <td>{{ $x->customer->nama }}</td>
                    <td>{{ $x->customer->nohp }}</td>
                    <td>{{ $x->point }}</td>
                    <td>{{ $x->wd_reason }}</td>
                    <td>{{ $x->created_at }}</td>
                </tr>
            @endforeach

            <tr class="total">
                <td colspan="2"></td>
                <td>{{ number_format($totalWD, 0, ',', '.') }} Point</td>
            </tr>
            <tr class="total">
                <td colspan="2"></td>
                <td>Point Saat Ini  {{ $total_point - $totalWD }} Point</td>
            </tr>
        </table>
    </div>
</body>

</html>
