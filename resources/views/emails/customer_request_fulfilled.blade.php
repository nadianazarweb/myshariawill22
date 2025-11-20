@extends('emails/layout/master')
@section('heading')
Your Request Has Been Fulfilled
@endsection
@section('main_section')

<tr data-element="colibri-bm-paragraph" data-label="Paragraphs">
    <td class="center-text" data-text-style="Paragraphs" align="center"
        style="font-family:'Poppins',Arial,Helvetica,sans-serif;font-size:14px;line-height:24px;font-weight:400;font-style:normal;color:#333333;text-decoration:none;letter-spacing:0px;">
        <singleline>
            <div mc:edit data-text-edit>
                Your Request
            </div>
        </singleline>
        <singleline>
            <div mc:edit data-text-edit>
                {{ $RequestRemarks }}
            </div>
        </singleline>
    </td>
</tr>
@endsection