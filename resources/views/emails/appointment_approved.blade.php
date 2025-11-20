@extends('emails/layout/master')
@section('heading')
Appointment Approved
@endsection

@section('main_section')
<!-- <tr data-element="colibri-bm-headline" data-label="Headlines">
    <td height="15" style="font-size:15px;line-height:15px;" data-height="Spacing under headline">&nbsp;</td>
</tr> -->
<tr data-element="colibri-bm-paragraph" data-label="Paragraphs">
    <td class="center-text" data-text-style="Paragraphs" align="center"
        style="font-family:'Poppins',Arial,Helvetica,sans-serif;font-size:14px;line-height:24px;font-weight:400;font-style:normal;color:#333333;text-decoration:none;letter-spacing:0px;">
        <singleline>
            <div mc:edit data-text-edit>
                Appointment Details
            </div>
        </singleline>
    </td>
</tr>


<tr data-element="colibri-bm-paragraph" data-label="Paragraphs">
    <td class="center-text" data-text-style="Paragraphs" align="center"
        style="font-family:'Poppins',Arial,Helvetica,sans-serif;font-size:14px;line-height:24px;font-weight:400;font-style:normal;color:#333333;text-decoration:none;letter-spacing:0px;">
        <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" class="row" width="89.66%"
            style="width:89.66%;max-width:89.66%;">
            <tr>
                <td height="25" style="font-size:25px;line-height:25px;">&nbsp;</td>
            </tr>
            <tr>
                <td align="center" valign="top" width="40%" style="width:40%;max-width:40%;">
                    <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" width="100%"
                        style="width:100%;max-width:100%;">
                        <tr>
                            <td data-text-style="Invoice Content" align="left"
                                style="font-family:'Poppins',Arial,Helvetica,sans-serif;font-size:16px;line-height:26px;font-weight:700;font-style:normal;color:#000000;text-decoration:none;letter-spacing:0px;">
                                <singleline>
                                    <div mc:edit data-text-edit>
                                        Date:
                                    </div>
                                </singleline>
                            </td>

                            <td data-text-style="Invoice Content" align="left"
                                style="font-family:'Poppins',Arial,Helvetica,sans-serif;font-size:16px;line-height:26px;font-weight:400;font-style:normal;color:#000000;text-decoration:none;letter-spacing:0px;">
                                <singleline>
                                    <div mc:edit data-text-edit>
                                        {{ date('d/m/Y', strtotime($ForDate)) }}
                                    </div>
                                </singleline>
                            </td>


                        </tr>

                        <tr>
                            <td data-text-style="Invoice Content" align="left"
                                style="font-family:'Poppins',Arial,Helvetica,sans-serif;font-size:16px;line-height:26px;font-weight:700;font-style:normal;color:#000000;text-decoration:none;letter-spacing:0px;">
                                <singleline>
                                    <div mc:edit data-text-edit>
                                        Time Slot:
                                    </div>
                                </singleline>
                            </td>

                            <td data-text-style="Invoice Content" align="left"
                                style="font-family:'Poppins',Arial,Helvetica,sans-serif;font-size:16px;line-height:26px;font-weight:400;font-style:normal;color:#000000;text-decoration:none;letter-spacing:0px;">
                                <singleline>
                                    <div mc:edit data-text-edit>
                                        {{ date('H:i', strtotime($TimeSlotStart)) }} - {{ date('H:i',
                                        strtotime($TimeSlotEnd)) }}
                                    </div>
                                </singleline>
                            </td>


                        </tr>
                    </table>
                </td>
                <td align="left" width="4%" style="width:4%;max-width:4%;"></td>

            </tr>
            <!-- <tr>
                                                <td height="25" style="font-size:25px;line-height:25px;">&nbsp;</td>
                                            </tr> -->
        </table>
    </td>
</tr>
@endsection