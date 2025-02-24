<html>

<head>
    <title>Antrian</title>
    <style>
        #tabel {
            font-size: 15px;
            border-collapse: collapse;
        }

        #tabel td {
            padding-left: 1px;
            border: 1px solid black;
        }
    </style>
    <script>
        window.print();
        window.onafterprint = function() {
            setTimeout(function() {
                window.close();
            }, 1000);
        }
    </script>
</head>

<body style='font-family:tahoma; font-size:5pt;'>
    <center>

        <table style='width:210px; font-size:16pt; font-family:sans-seri; border-collapse: collapse;' border='0'>
            <tr>
                <td width='70%' align='CENTER' vertical-align:top'><span style='color:black;'>
                        <img src="{{ asset('images/bdc.png') }}" style="width: 200px; max-width: 300px" /> <br>
                </td>
            </tr>

            <td width='50%' align='CENTER' vertical-align:top'>
                <span style='color:black; font-size:18pt'>
                    <b>{{ strtoupper($antrian->lokasiloket->lokasi_loket) }}</b><br><br></span>
                <span style='font-size:18pt'>Nomor Antrian : </span> <br>
                <span style='font-size:80pt'>{{ $antrian->prefix }} {{ $antrian->nomor }}</span>
            </td>

        </table>

        <style>
            hr {
                display: block;
                margin-top: 0.5em;
                margin-bottom: 0.5em;
                margin-left: auto;
                margin-right: auto;
                border-style: inset;
                border-width: 1px;
            }
        </style>
        <br>
        <br>
        <table style='width:210px; font-size:11pt; font-family:sans-seri; border-collapse: collapse;' border='0'>

            <td width='70%' align='CENTER' vertical-align:top><span style='color:black;'>
                    <span style='font-size:12pt'>TANGGAL : {{ date('d-m-Y H:i', strtotime($antrian->created_at)) }}</span>

            </td>

        </table>

    </center>
</body>

</html>
