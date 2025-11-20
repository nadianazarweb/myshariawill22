@extends('emails/layout/master')
@section('heading')
Confirmation Email
@endsection
@section('main_section')

<tr data-element="colibri-bm-paragraph" data-label="Paragraphs">
    <td class="center-text" data-text-style="Paragraphs" align="center"
        style="font-family:'Poppins',Arial,Helvetica,sans-serif;font-size:14px;line-height:24px;font-weight:400;font-style:normal;color:#333333;text-decoration:none;letter-spacing:0px;text-align:left">
        <singleline>
            <div mc:edit data-text-edit>
                As Salam Alaykum {{ $FullName }},
                <br><br>
                You are one step closer to your Sharia compliant will. This email is just to confirm that we have
                received your submission to My Sharia Will.
                <br><br>
                Please review your responses and reply to us within 48 hours to let us know if there is anything you
                would like us to amend.
                <br><br>
                Please note that if you have a property held as "joint tenants" then your property will automatically go
                to your partner in the property and will not form part of your estate. Should you wish the property to
                form part of your estate you can either formally change the title to a “tenants-in-common” or you can
                informally agree with your co-owner in the property that they will distribute the property Islamically
                after you pass away to the rightful heirs even though the legal title is in your co-owners name.
                <br><br>
                We will get back to you within ten days, Insha Allah. In the meantime, If we have any questions or
                issues, we will be in touch.
            </div>
        </singleline>
        <singleline>
            <div mc:edit data-text-edit>
            </div>
        </singleline>
        <singleline>
            <div mc:edit data-text-edit>

            </div>
        </singleline>
    </td>
</tr>

@endsection