@component('mail::message')
# Welcome To Adukan Store

Please Click The Link Below To View Your Cart

@component('mail::button', ['url' => $array['url']])
Click Here
@endcomponent

@php
	echo $array['content'];
@endphp

@php
	echo $array['url'];
@endphp



Thanks,<br>
{{ config('app.name') }}
@endcomponent
