@extends('frontend.layouts.app')

@section('content')

    <section class="gry-bg py-4 profile">
        <div class="container">
            <div class="row cols-xs-space cols-sm-space cols-md-space">
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="sidebar sidebar--style-3 no-border stickyfill p-0">
                        <div class="widget mb-0">
                            <div class="widget-profile-box text-center p-3">
                                @if ($user->avatar_original != null)
                                    <div class="image" style="background-image:url('{{ my_asset($user->avatar_original) }}')"></div>
                                @else
                                    <img src="{{ my_asset('frontend/images/user.png') }}" class="image rounded-circle">
                                @endif
                                <div class="name">{{ $user->name }}</div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-lg-9">
                    <div class="main-content">

                        <div class="page-title">
                            <div class="row align-items-center">
                                <div class="col-md-6 col-12">
                                    <h2 class="heading heading-6 text-capitalize strong-600 mb-0">

                                    </h2>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="float-md-right">
                                        <ul class="breadcrumb">
                                            <li><a href="{{ route('home') }}">{{ translate('Home') }}</a></li>

                                            <li class="active"><a href="{{ route('profile') }}">{{ $user->name }}</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <div class="form-box bg-white mt-4">
                                <div class="form-box-title px-3 py-2">
                                    {{ translate('Basic info') }}
                                </div>
                                <div class="form-box-content p-3">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('Name') }}</label>
                                        </div>
                                        <div class="col-md-10">
                                            <p> {{ $user->name }} </p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('Phone')}}</label>
                                        </div>
                                        <div class="col-md-10">
                                            <p> {{ $user->phone }} </p>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>{{ translate('Email')}}</label>
                                        </div>
                                        <div class="col-md-10">
                                            <p> {{ $user->email }} </p>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="form-box bg-white mt-4">
                                <div class="form-box-title px-3 py-2">
                                    {{translate('Personal info')}}
                                </div>
                                <div class="form-box-content" style="padding: 10px 30px;">
                                    <div class="row gutters-10">

                                        {!! $user->bio !!}
                                </div>
                            </div>

                             </div>

                             <div class="form-box bg-white mt-4">
                                <div class="form-box-title px-3 py-2">
                                    {{translate('Address')}}
                                </div>
                                <div class="form-box-content" style="padding: 10px 30px;">

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>{{ translate('country') }}</label>
                                    </div>
                                    <div class="col-md-10">
                                        <p> {{ $user->country }} </p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>{{ translate('City') }}</label>
                                    </div>
                                    <div class="col-md-10">
                                        <p> {{ $user->city }} </p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>{{ translate('address') }}</label>
                                    </div>
                                    <div class="col-md-10">
                                        <p> {{ $user->address }} </p>
                                    </div>
                                </div>
                             </div>
                </div>
            </div>
        </div>
    </section>



@endsection
