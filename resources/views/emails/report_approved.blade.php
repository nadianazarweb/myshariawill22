@extends('emails/layout/master')
@section('heading')
Your Report Is Ready
@endsection

@section('main_section')


<tr data-element="colibri-bm-paragraph" data-label="Paragraphs">
    <td class="center-text" data-text-style="Paragraphs" align="center"
        style="font-family:'Poppins',Arial,Helvetica,sans-serif;font-size:14px;line-height:24px;font-weight:400;font-style:normal;color:#333333;text-decoration:none;letter-spacing:0px;;text-align:left">
        <singleline>
            <div mc:edit data-text-edit>
                As Salam Alaykum {{ $UserFullName }},
                <br><br>
                The drafting of your will is now complete and is ready for you to finalise! 
                <br><br>
                The only thing that remains for you to do is to sign your will in the presence of two witnesses. 
                <br><br>
                We have attached two documents to this email:
                <br>
                <ol>
                    <li>Your finalised and complete Sharia Will</li>
                    <li>Instructions on how to sign your will</li>
                </ol>
                Please follow the instructions on how to sign your will carefully as it is really important you follow these steps as otherwise your will may not be legally valid.
                <br><br>
                To make an unlimited number of future updates please sign up to our annual £10 direct debit <a href="https://pay.gocardless.com/flow/RE00130A544NMQR1G206EHCZYCMP1V9B" style="text-decoration: underline;font-weight: bold;color: dodgerblue;" target="_blank">here</a>. Otherwise it is £30 for ad-hoc will updates.
                <br><br>
                Please do take a moment to leave us a review on <a href="https://uk.trustpilot.com/review/islamicfinanceguru.com?utm_medium=trustbox&&utm_source=MicroReviewCount" style="text-decoration: underline;font-weight: bold;color: dodgerblue;" target="_blank">Trustpilot</a>. It will take you just a few second but means a lot to us and helps others too!
                <br><br>
                Thank you for choosing My Sharia Will.
            </div>
        </singleline>
    </td>
</tr>
<!-- <tr data-element="colibri-bm-button" data-label="Buttons">
    <td align="center">
        <table border="0" cellspacing="0" cellpadding="0" role="presentation" align="center" class="center-float">
            <tr>
                <td align="center" data-border-radius-default="0,6,36" data-border-radius-custom="Buttons"
                    data-bgcolor="Buttons" bgcolor="#45517f" style="border-radius: 36px;">

                    <singleline>
                        <a href="{{ $url }}" mc:edit data-button data-text-style="Buttons"
                            style="font-family:'Poppins',Arial,Helvetica,sans-serif;font-size:16px;line-height:20px;font-weight:700;font-style:normal;color:#FFFFFF;text-decoration:none;letter-spacing:0px;padding: 15px 35px 15px 35px;display: inline-block;"><span>SEE REPORT</span></a>
                    </singleline>

                </td>
            </tr>
        </table>
    </td>
</tr> -->
@endsection