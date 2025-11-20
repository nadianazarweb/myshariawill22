@extends('emails/layout/master')
@section('heading')
Email Verification
@endsection

@section('main_section')


<tr data-element="colibri-bm-paragraph" data-label="Paragraphs">
    <td class="center-text" data-text-style="Paragraphs" align="center"
        style="font-family:'Poppins',Arial,Helvetica,sans-serif;font-size:14px;line-height:24px;font-weight:400;font-style:normal;color:#333333;text-decoration:none;letter-spacing:0px;">
        <singleline>
            <div mc:edit data-text-edit>
                Thank you for signing up! To activate your account, please click the link below:
            </div>
        </singleline>
    </td>
</tr>




<tr data-element="colibri-bm-paragraph" data-label="Paragraphs">
    <td height="25" style="font-size:25px;line-height:25px;" data-height="Spacing under paragraph">&nbsp;</td>
</tr>
<tr data-element="colibri-bm-button" data-label="Buttons">
    <td align="center">
        <table border="0" cellspacing="0" cellpadding="0" role="presentation" align="center" class="center-float">
            <tr>
                <td align="center" data-border-radius-default="0,6,36" data-border-radius-custom="Buttons"
                    data-bgcolor="Buttons" bgcolor="#45517f" style="border-radius: 36px;">

                    <singleline>
                        <a href="{{ $url }}" mc:edit data-button data-text-style="Buttons"
                            style="font-family:'Poppins',Arial,Helvetica,sans-serif;font-size:16px;line-height:20px;font-weight:700;font-style:normal;color:#FFFFFF;text-decoration:none;letter-spacing:0px;padding: 15px 35px 15px 35px;display: inline-block;"><span>VERIFY EMAIL</span></a>
                    </singleline>

                </td>
            </tr>
        </table>
    </td>
</tr>
@endsection