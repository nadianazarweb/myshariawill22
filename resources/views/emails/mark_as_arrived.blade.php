@extends('emails/layout/master')
@section('heading')
Thank you for being punctual!
@endsection
@section('main_section')

<tr data-element="colibri-bm-paragraph" data-label="Paragraphs">
    <td class="center-text" data-text-style="Paragraphs" align="center"
        style="font-family:'Poppins',Arial,Helvetica,sans-serif;font-size:14px;line-height:24px;font-weight:400;font-style:normal;color:#333333;text-decoration:none;letter-spacing:0px;">
        <singleline>
            <div mc:edit data-text-edit>
                If you have any questions or need further assistance, please don't hesitate to contact us.
            </div>
        </singleline>
        <singleline>
            <div mc:edit data-text-edit>
                We look forward to serve you
            </div>
        </singleline>
    </td>
</tr>

@endsection