<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Invoice</title>

    <style>
    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        font-size: 16px;
        line-height: 24px;
        font-family: font-family: 'Roboto', Helvetica, sans-serif;
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
    }

    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.item td {
        border-bottom: 1px solid #eee;
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
        font-family: 'Roboto', Helvetica, sans-serif;
    }

    .invoice-box.rtl table {
        text-align: right;
    }

    .invoice-box.rtl table tr td:nth-child(2) {
        text-align: left;
    }

    table.border_cls td {
        border: 1px solid;
    }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="simple_title">
                                <img src="{{ url('/') }}/images/logo.png" style="max-width: 200px" /><br />
                                P O BOX 30105<br />
                                Brampton, ON L6R 0S9<br />
                                <table class="border_cls" cellpadding="0" cellspacing="0" style="width:50%">
                                    <tr>
                                        <td>Phone #</td>
                                        <td>647-6800-2425</td>
                                    </tr>
                                    <tr>
                                        <td>Office #</td>
                                        <td>905-599-0990</td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td>jc@japgobindtransport.ca</td>
                                    </tr>
                                    <tr>
                                        <td>GST/HST</td>
                                        <td>824726889</td>
                                    </tr>
                                </table>
                            </td>

                            <td>
                                <b>Invoice</b><br />
                                <table class="border_cls" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>date</td>
                                        <td>Invoice #</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $dispatch_data['invoice_date'] }}</td>
                                        <td>{{ $dispatch_data['invoice_id'] }}</td>
                                    </tr>
                                </table><br />
                                <table class="border_cls" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>Invoice To</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $dispatch_data['get_user']['name'] }}
                                        </td>
                                    </tr>
                                </table><br />
                                <table class="border_cls" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>P.O.NO.</td>
                                        <td>Term</td>
                                        <td>Due Date</td>
                                        <td>Project/ Job</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="2">

                    <table class="border_cls" cellpadding="0" cellspacing="0" style="width:100%">
                        <tr>
                            <td>Work By</td>
                            <td>Date</td>
                            <td>Pickup Location</td>
                            <td>Destination</td>
                            <td>Ticket #</td>
                            <td>Total Load / Hourly</td>
                           <!-- <td>Rate</td>-->
                            <td>Amount</td>
                        </tr>
                        @php
						
                        foreach($dispatch_data_all as $d_val){
                        $total_load_hour = $total_income = 0;
                        $total_income = $d_val['expense'];
                        $total_load_hour = $d_val['hour_or_load'];
                        @endphp
                        <tr>
                            <td> {{ ($d_val['get_dispatch']['job_type'] == 'load') ? 'Load' : 'Hourly'}}</td>
                            <td>{{ date('d/m/Y',strtotime($d_val['get_dispatch']['start_time']))}}</td>
                            <td>{{ $d_val['get_dispatch']['start_location'] }}</td>
                            <td>{{ $d_val['get_dispatch']['dump_location'] }}</td>
                            <td>{{ $d_val['ticket_number']}}</td>
                            <td> {{ $total_load_hour }}</td>
                            <!--<td>{{ $d_val['get_dispatch']['job_rate'] }}</td>-->
                            <td>${{ $total_income }}</td>
                        </tr>
                        @php

                        }
                        @endphp

                        <tr>
                            <td colspan="5" style="text-align:center;">HST on Sale</td>
                            <td>{{$hst_per}}%</td>
                            <td>${{ $hst_per_amt }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="width:60%">
                    {{ $dispatch_data['business_note'] }}
                </td>
                <td style="width:40%">
                    <table>
                        <tr class="heading">
                            <td>Item</td>
                            <td>Price</td>
                        </tr>

                        <tr class="item">
                            <td>Subtotal</td>
                            <td>${{ $all_total_income }}</td>
                        </tr>

                        <tr class="item">
                            <td>GST/ HST</td>
                            <td>${{ $hst_per_amt }}</td>
                        </tr>

                        <tr class="item last">
                            <td>Total Amount</td>
                            <td>${{ $balance_due }}</td>
                        </tr>

                        <tr class="total">
                            <td></td>
                            <td>Total: ${{ $balance_due }}</td>
                        </tr>
                    </table>
                </td>

            </tr>

        </table>
    </div>
</body>

</html>