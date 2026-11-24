<!DOCTYPE html>
@php
    $site_direction = session()->get('provider_site_direction');
@endphp
<html lang="en" dir="{{$site_direction}}">

<head>
    <title>@yield('title')</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php($favIcon = getBusinessSettingsImageFullPath(key: 'business_favicon', settingType: 'business_information', path: 'business/',  defaultPath : 'public/assets/admin-module/img/media/upload-file.png'))
    <link rel="shortcut icon" href="{{ $favIcon }}"/>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
            href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
            rel="stylesheet">

    {{-- old icon link --}}
    <link href="{{asset('public/assets/provider-module')}}/css/material-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('public/assets/provider-module')}}/plugins/webfonts/uicons-regular-rounded.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/provider-module')}}/plugins/webfonts/uicons-solid-rounded.css"/>

    <link rel="stylesheet" href="{{asset('public/assets/provider-module')}}/css/bootstrap.min.css"/>
    <link rel="stylesheet"
          href="{{asset('public/assets/provider-module')}}/plugins/perfect-scrollbar/perfect-scrollbar.min.css"/>

    <link rel="stylesheet" href="{{asset('public/assets/provider-module')}}/plugins/apex/apexcharts.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/provider-module')}}/plugins/select2/select2.min.css"/>

    <link rel="stylesheet" href="{{asset('public/assets/provider-module')}}/css/toastr.css">

    <link rel="stylesheet" href="{{asset('public/assets/provider-module')}}/css/style.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/provider-module')}}/css/dev.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/provider-module')}}/css/dev-tahir-provider.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/common')}}/css/common.css"/>

    @stack('css_or_js')
    <style>
        @keyframes progress-animation {
            from {
                --progress: 0;
            }
            to {
                --progress: 70;
            }
        }
    </style>
</head>

<body>
<script>
    localStorage.theme && document.querySelector('body').setAttribute("data-bs-theme", localStorage.theme);
</script>

<div class="offcanvas-overlay"></div>

<div class="preloader"></div>

@include('providermanagement::layouts.partials._header')

@include('providermanagement::layouts.partials._aside')

@include('providermanagement::layouts.partials._settings-sidebar')

<main class="main-area">
    @yield('content')

    @include('providermanagement::layouts.partials._footer')

    @if(env('APP_ENV') == 'demo')
        <div class="alert alert--message-2 alert-dismissible fade show" id="demo-reset-warning">
            <img width="28" class="align-self-start" src="{{ asset('public/assets/admin-module/img/info-2.png') }}" alt="">
            <div class="w-0 flex-grow-1">
                <h6>{{ translate('warning').'!'}}</h6>
                <span class="warning-message">
            {{translate('though_it_is_a_demo_site').'.'.translate('_our_system_automatically_reset_after_one_hour_&_that_is_why_you_logged_out').'.'}}
        </span>
            </div>
            <button type="button" class="btn-close p-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @include('providermanagement::layouts.partials.subscription-modal')
    @include('adminmodule::layouts.partials.image-view-modal')

    {{-- Service Request Modal --}}
    <div class="modal fade" id="serviceRequestModal" tabindex="-1"
         aria-labelledby="serviceRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="--bs-modal-width: 430px">
            <div class="modal-content">
            </div>
        </div>
    </div>
</main>

<?php
$serviceAtProviderPlace = (int)((business_config('service_at_provider_place', 'provider_config'))->live_values ?? 0);
$serviceLocations = getProviderSettings(providerId: auth()->user()->provider->id, key: 'service_location', type: 'provider_config') ?? ['customer'];
?>


<script src="{{asset('public/assets/provider-module')}}/js/jquery-3.6.0.min.js"></script>
<script src="{{asset('public/assets/provider-module')}}/js/bootstrap.bundle.min.js"></script>
<script src="{{asset('public/assets/provider-module')}}/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="{{asset('public/assets/provider-module')}}/plugins/spartan-multi-image-picker/spartan-multi-image-picker-min.js"></script>
<script src="{{asset('public/assets/provider-module')}}/js/main.js"></script>
<script src="{{asset('public/assets/common')}}/js/common-image-upload.js"></script>
<script src="{{asset('public/assets/common')}}/js/common.js"></script>


<script src="{{asset('public/assets/provider-module')}}/plugins/select2/select2.min.js"></script>

<script src="{{asset('public/assets/provider-module')}}/js/sweet_alert.js"></script>
<script src="{{asset('public/assets/provider-module')}}/js/toastr.js"></script>
<script src="{{asset('public/assets/provider-module')}}/js/dev.js"></script>
<script src="{{asset('public/assets/provider-module')}}/js/keyword-highlight.js"></script>

{{--country code --}}
<span class="system-default-country-code" data-value="us"></span>
<link rel="stylesheet" href="{{asset('public/assets/libs/intl-tel-input/css/intlTelInput.css')}}"/>
<script src="{{ asset('public/assets/libs/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ asset('public/assets/libs/intl-tel-input/js/utils.js') }}"></script>
<script src="{{ asset('public/assets/libs/intl-tel-input/js/intlTelInout-validation.js') }}"></script>


<script src="{{asset('public/assets/admin-module/js/firebase.min.js')}}"></script>
<script src="{{asset('public/assets/admin-module')}}/js/keyword-highlight.js"></script>




<audio id="audio-element">
    <source src="{{asset('public/assets/provider-module')}}/sound/notification.mp3" type="audio/mpeg">
</audio>

<script>
    "use strict";

    $(document).ready(function () {
        $('.js-select').select2();
    });

    function checkDemoResetTime() {
        let currentMinute = new Date().getMinutes();
        if (currentMinute > 55 && currentMinute <= 60) {
            $('#demo-reset-warning').addClass('active');
        } else {
            $('#demo-reset-warning').removeClass('active');
        }
    }
    checkDemoResetTime();
    setInterval(checkDemoResetTime, 60000);

    $("#search-form__input").on("keyup", function () {
        var value = this.value.toLowerCase().trim();
        $(".show-search-result a").show().filter(function () {
            return $(this).text().toLowerCase().trim().indexOf(value) == -1;
        }).hide();
    });

    function checkBooking(count) {
        // console.log(count)
        sessionStorage.setItem("booking_count", parseInt(count));
    }

    function update_notification() {
        let count = $('#notification_count').text();
        let notification_count = sessionStorage.getItem("notification_count");

        if (parseInt(count) > 0) {
            sessionStorage.setItem("notification_count", parseInt(notification_count) + parseInt(count));
        }
    }


    @if(!request()->is('provider/profile-update') && is_null(auth()->user()->provider->coordinates))
    Swal.fire({
        title: "{{translate('Update Location')}}",
        text: "{{translate('You must update your location first')}}",
        type: 'warning',
        showCloseButton: false,
        showCancelButton: false,
        confirmButtonColor: 'var(--bs-primary)',
        confirmButtonText: "{{translate('Update from profile')}}",
        reverseButtons: true
    }).then((result) => {
        if (result.value) {
            location.href = "{{route('provider.profile_update')}}";
        }
    })
    @endif


    $('.form-alert').on('click', function () {
        let id = $(this).data('id');
        let message = $(this).data('message');
        let title = $(this).data('title');
        form_alert(id, message, title)
    });

    function form_alert(id, message, title = 'are_you_sure?') {
        Swal.fire({
            title: title,
            text: message,
            type: 'warning',
            showCloseButton: true,
            showCancelButton: true,
            cancelButtonColor: 'var(--bs-secondary)',
            confirmButtonColor: 'var(--bs-primary)',
            cancelButtonText: 'No',
            confirmButtonText: 'Yes',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $('#' + id).submit()
            }
        })
    }

    $('.route-alert').on('click', function () {
        let route = $(this).data('route');
        let message = $(this).data('message');
        route_alert(route, message)
    });

    function route_alert(route, message) {
        Swal.fire({
            title: "{{translate('are_you_sure')}}?",
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'var(--bs-secondary)',
            confirmButtonColor: 'var(--bs-primary)',
            cancelButtonText: '{{translate('Cancel')}}',
            confirmButtonText: '{{translate('Yes')}}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $.get({
                    url: route,
                    dataType: 'json',
                    data: {},
                    beforeSend: function () {
                    },
                    success: function (data) {
                        console.log(data)
                        toastr.success(data.message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    },
                    complete: function () {
                    },
                });
            }
        })
    }

    $('.route-alert-reload').on('click', function (){
        let route = $(this).data('route');
        let message = $(this).data('message');
        route_alert_reload(route, message, true);
    });

    function route_alert_reload(route, message, reload, status = null, id = null) {
        Swal.fire({
            title: "{{translate('are_you_sure')}}?",
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'var(--bs-secondary)',
            confirmButtonColor: 'var(--bs-primary)',
            cancelButtonText: 'Cancel',
            confirmButtonText: 'Yes',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $.get({
                    url: route,
                    dataType: 'json',
                    data: {},
                    beforeSend: function () {

                    },
                    success: function (data) {
                        if (reload) {
                            setTimeout(location.reload.bind(location), 1000);
                        }
                        toastr.success(data.message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    },
                    complete: function () {

                    },
                });
            }else {
                if (status === 1) $(`#${id}`).prop('checked', false);
                if (status === 0) $(`#${id}`).prop('checked', true);
            }
        })
    }



    $('.update-notification').on('click', function () {
        update_notification()
    });

    $('.provider-logout').on('click', function (event) {
        Swal.fire({
            title: "{{translate('are_you_sure')}}?",
            text: "{{translate('want_to_logout')}}",
            type: 'warning',
            showCloseButton: true,
            showCancelButton: true,
            cancelButtonColor: 'var(--bs-secondary)',
            confirmButtonColor: 'var(--bs-primary)',
            cancelButtonText: 'Cancel',
            confirmButtonText: 'Yes',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                location.href = "{{route('provider.auth.logout')}}"
            }
        })
    });


    $(document).ready(function () {
        $('#searchForm input[name="search"]').keyup(function () {
            var searchKeyword = $(this).val().trim();
            console.log(searchKeyword);
            if (searchKeyword.length >= 2) {
                $('#searchResults').empty().html('<div class="text-center text-muted py-5">{{translate('Searching....')}}</div>');
                $.ajax({
                    type: 'POST',
                    url: $('#searchForm').attr('action'),
                    data: {search: searchKeyword, _token: $('input[name="_token"]').val()},
                    success: function (response) {
                        var resultHtml = '';
                        $('#searchResults').empty().html(response.htmlView);
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            } else {
                $('#searchResults').html('<div class="text-center text-muted py-5">{{translate('Write a minimum of two characters.')}}</div>');
            }
        });
    });

    $(document).ready(function () {
        $("#staticBackdrop").on("shown.bs.modal", function () {
            $(this).find("#searchForm input[type=search]").val('');
            $('#searchResults').html('<div class="text-center text-muted py-5">{{translate('Loading recent searches')}}...</div>');
            $(this).find("#searchForm input[type=search]").focus();
            $.ajax({
                type: 'GET',
                url: '{{ route('provider.recent.search') }}',
                success: function (response) {
                    $('#searchResults').html(response.htmlView);
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    $('#searchResults').html('<div class="text-center text-muted py-5">{{translate('Error loading recent searches')}}.</div>');
                }
            });
        });
    });
    $(document).ready(function (){
        const platform = navigator.platform;
        let shortcutText = '';
        let isMac = false;

        if (platform.toLowerCase().includes('mac')) {
            shortcutText = 'Cmd+K';
            isMac = true;
        } else if (platform.toLowerCase().includes('linux') || platform.toLowerCase().includes('win')) {
            shortcutText = 'Ctrl+K';
            isMac = false;
        } else {
            shortcutText = 'Ctrl+K';
            isMac = false;
        }
        $('.ctrlplusk').text(shortcutText);
    });



    {{--$(document).ready(function () {--}}
    {{--    $('#searchForm input[name="search"]').keyup(function () {--}}
    {{--        var searchKeyword = $(this).val().trim();--}}

    {{--        if (searchKeyword.length >= 0) {--}}
    {{--            $.ajax({--}}
    {{--                type: 'POST',--}}
    {{--                url: $('#searchForm').attr('action'),--}}
    {{--                data: {search: searchKeyword, _token: $('input[name="_token"]').val()},--}}
    {{--                success: function (response) {--}}
    {{--                    if (response.length === 0) {--}}
    {{--                        $('#searchResults').html('<div class="text-center text-muted py-5">{{translate('No results found')}}</div>');--}}
    {{--                    } else {--}}
    {{--                        var resultHtml = '';--}}
    {{--                        response.forEach(function (route) {--}}
    {{--                            resultHtml += '<a href="' + route.fullRoute + '" class="search-list-item d-flex flex-column" data-route-name="' + route.routeName + '" data-route-uri="' + route.URI + '" data-route-full-url="' + route.fullRoute + '" aria-current="true">';--}}
    {{--                            resultHtml += '<h5>' + route.routeName + '</h5>';--}}
    {{--                            resultHtml += '<p class="text-muted fs-12">' + route.URI + '</p>';--}}
    {{--                            resultHtml += '</a>';--}}
    {{--                        });--}}
    {{--                        $('#searchResults').html('<div class="search-list d-flex flex-column">' + resultHtml + '</div>');--}}

    {{--                        $('.search-list-item').click(function () {--}}
    {{--                            var routeName = $(this).data('route-name');--}}
    {{--                            var routeUri = $(this).data('route-uri');--}}
    {{--                            var routeFullUrl = $(this).data('route-full-url');--}}

    {{--                            $.ajax({--}}
    {{--                                type: 'POST',--}}
    {{--                                url: '{{ route('provider.search.routing.store') }}',--}}
    {{--                                data: {--}}
    {{--                                    routeName: routeName,--}}
    {{--                                    routeUri: routeUri,--}}
    {{--                                    routeFullUrl: routeFullUrl,--}}
    {{--                                    searchKeyword: searchKeyword,--}}
    {{--                                    _token: $('input[name="_token"]').val()--}}
    {{--                                },--}}
    {{--                                success: function (response) {--}}
    {{--                                    console.log(response.message);--}}
    {{--                                },--}}
    {{--                                error: function (xhr, status, error) {--}}
    {{--                                    console.error(xhr.responseText);--}}
    {{--                                }--}}
    {{--                            });--}}
    {{--                        });--}}
    {{--                    }--}}
    {{--                },--}}
    {{--                error: function (xhr, status, error) {--}}
    {{--                    console.error(xhr.responseText);--}}
    {{--                }--}}
    {{--            });--}}
    {{--        } else {--}}
    {{--            $('#searchResults').html('<div class="text-center text-muted py-5">{{translate('Write a character.')}}.</div>');--}}
    {{--        }--}}
    {{--    });--}}
    {{--});--}}

    {{--$(document).ready(function () {--}}
    {{--    $("#staticBackdrop").on("shown.bs.modal", function () {--}}
    {{--        $(this).find("#searchForm input[type=search]").val('');--}}
    {{--        $('#searchResults').html('<div class="text-center text-muted py-5">{{translate('Loading recent searches')}}...</div>');--}}
    {{--        $(this).find("#searchForm input[type=search]").focus();--}}

    {{--        $.ajax({--}}
    {{--            type: 'GET',--}}
    {{--            url: '{{ route('provider.recent.search') }}',--}}
    {{--            success: function (response) {--}}
    {{--                if (response.length === 0) {--}}
    {{--                    $('#searchResults').html('<div class="text-center text-muted py-5">{{translate('It appears that you have not yet searched.')}}.</div>');--}}
    {{--                } else {--}}
    {{--                    var resultHtml = '';--}}
    {{--                    response.forEach(function (route) {--}}
    {{--                        resultHtml += '<a href="' + route.route_full_url + '" class="search-list-item d-flex flex-column" data-route-name="' + route.route_name + '" data-route-uri="' + route.route_uri + '" data-route-full-url="' + route.route_full_url + '" aria-current="true">';--}}
    {{--                        resultHtml += '<h5>' + route.route_name + '</h5>';--}}
    {{--                        resultHtml += '<p class="text-muted fs-12  mb-0">' + route.route_uri + '</p>';--}}
    {{--                        resultHtml += '</a>';--}}
    {{--                    });--}}
    {{--                    $('#searchResults').html('<div class="recent-search fs-16 fw-500 animate">' +--}}
    {{--                        @json(translate('Recent Search')) + '<div class="search-list d-flex flex-column mt-2">' + resultHtml + '</div></div>');--}}

    {{--                    $('.search-list-item').click(function () {--}}
    {{--                        var routeName = $(this).data('route-name');--}}
    {{--                        var routeUri = $(this).data('route-uri');--}}
    {{--                        var routeFullUrl = $(this).data('route-full-url');--}}
    {{--                        var searchKeyword = $('input[type=search]').val().trim();--}}

    {{--                        $.ajax({--}}
    {{--                            type: 'POST',--}}
    {{--                            url: '{{ route('provider.search.routing.store') }}',--}}
    {{--                            data: {--}}
    {{--                                routeName: routeName,--}}
    {{--                                routeUri: routeUri,--}}
    {{--                                routeFullUrl: routeFullUrl,--}}
    {{--                                searchKeyword: searchKeyword,--}}
    {{--                                _token: $('input[name="_token"]').val()--}}
    {{--                            },--}}
    {{--                            success: function (response) {--}}
    {{--                                console.log(response.message);--}}
    {{--                            },--}}
    {{--                            error: function (xhr, status, error) {--}}
    {{--                                console.error(xhr.responseText);--}}
    {{--                            }--}}
    {{--                        });--}}
    {{--                    });--}}
    {{--                }--}}
    {{--            },--}}
    {{--            error: function (xhr, status, error) {--}}
    {{--                console.error(xhr.responseText);--}}
    {{--                $('#searchResults').html('<div class="text-center text-muted py-5">{{translate('Loading recent searches')}}.</div>');--}}
    {{--            }--}}
    {{--        });--}}
    {{--    });--}}
    {{--});--}}

    document.addEventListener('keydown', function(event) {
        if (event.ctrlKey && event.key === 'k') {
            event.preventDefault();
            document.getElementById('modalOpener').click();
        }
    });

    // Search Modal Open Input Focus
    $(document).ready(function () {
        $("#staticBackdrop").on("shown.bs.modal", function () {
            $(this).find("#searchForm input[type=search]").val('');
            $('#searchResults').html('<div class="text-center text-muted py-5">{{ translate('It appears that you have not yet searched') }}.</div>');
            $(this).find("#searchForm input[type=search]").focus();
        });
    });

    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('search', function() {
        if (!this.value.trim()) {
            $('#searchResults').html('<div class="text-center text-muted py-5">{{ translate('It appears that you have not yet searched') }}.</div>');
        }
    });

    $('#searchForm').submit(function (event) {
        event.preventDefault();
    });


    $(document).ready(function(){
        $('.renew-package').on('click', function() {
            var packageId = $(this).data('id');

            $.ajax({
                url: '{{ route("provider.subscription-package.renew.ajax") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: packageId
                },
                success: function(response) {
                    $('.append-renew').html(response);
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });
    });

    $(document).ready(function(){
        $('.shift-package').on('click', function() {
            var packageId = $(this).data('id');

            $.ajax({
                url: '{{ route("provider.subscription-package.shift.ajax") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: packageId
                },
                success: function(response) {
                    $('.append-shift').html(response);
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });
    });

    $(document).ready(function(){
        $('.purchase-package').on('click', function() {
            var packageId = $(this).data('id');

            $.ajax({
                url: '{{ route("provider.subscription-package.purchase.ajax") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: packageId
                },
                success: function(response) {
                    $('.append-purchase').html(response);
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const trialNotificationCloseButtons = document.querySelectorAll('.trial-notification-close');

        trialNotificationCloseButtons.forEach(button => {
            button.addEventListener('click', function () {
                fetch('{{ route('provider.set.modal.closed') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({modalClosed: true})
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Modal closed:', data);
                    });
            });
        });
    });

    $(document).ready(function () {
        @if(session('showSubscriptionModal'))
        $('#subscriptionPlanModal').modal('show');
        @endif
        @if(session('paySubscriptionModal'))
        $('#paySubscriptionModal').modal('show');
        @endif
    });
</script>

<script>
    @php($admin_order_notification = (int) business_config('booking_notification', 'business_information')->live_values)
    @php($admin_order_notification_type = business_config('booking_notification_type', 'business_information')->live_values)

    @if($admin_order_notification)

        var audio = document.getElementById("audio-element");

        function playAudio(status) {
            if (!audio) return;
            status ? audio.play() : audio.pause();
        }

        @if($admin_order_notification_type == 'manual')

            @if(!auth()->user()?->provider?->is_suspended || !business_config('suspend_on_exceed_cash_limit_provider', 'provider_config')->live_values)
                setInterval(function () {
                    $.get({
                        url: '{{ route('provider.get_updated_data') }}',
                        dataType: 'json',
                        success: function (response) {
                            let data = response.data;

                            let notification_count = sessionStorage.getItem("notification_count");
                            if (notification_count == null || isNaN(notification_count)) {
                                notification_count = 0;
                                sessionStorage.setItem("notification_count", notification_count);
                            }

                            let booking_count = sessionStorage.getItem("booking_count");
                            if (booking_count == null || isNaN(parseInt(booking_count)) || data.booking === 0) {
                                booking_count = 0;
                                sessionStorage.setItem("booking_count", parseInt(booking_count));
                            }

                            let count = data.notification_count;

                            document.getElementById("message_count").innerHTML = data.message;
                            document.getElementById("notification_count").innerHTML = parseInt(count) < 0 ? 0 : parseInt(count);
                            document.getElementById("show-notification-list").innerHTML = data.notification_template;
                            var availableStatus = "{{auth()->user()?->provider?->service_availability}}";

                            if (data.booking !== parseInt(booking_count) && data.booking > 0 && availableStatus == 1) {
                                playAudio(true);
                                Swal.fire({
                                    title: '{{translate('New_Notification')}}',
                                    text: "{{translate('You_have_new_booking_arrived')}}",
                                    type: 'info',
                                    showCloseButton: true,
                                    showCancelButton: false,
                                    focusConfirm: false,
                                    confirmButtonText: '{{translate('Show_Bookings')}}',
                                }).then((result) => {
                                    if (result.value) {
                                        playAudio(false);
                                        checkBooking();
                                        location.href = "{{ route('provider.booking.list') }}?booking_status=pending&service_type=all";
                                    } else if (result.dismiss === 'close') {
                                        playAudio(false);
                                        checkBooking();
                                        location.href = "{{ route('provider.booking.list') }}?booking_status=pending&service_type=all";
                                    }
                                })
                            }

                            if (data.unchecked_posts > 0 && availableStatus == 1) {
                                playAudio(true);
                                if (data.post_content !== null) {
                                    $('#serviceRequestModal .modal-content').html(data.post_content);
                                }
                                $('#serviceRequestModal').modal('show');
                            }
                        },
                    });
                }, 10000);
             @endif

        @endif
    @endif

    function updateBellCount(count) {
        let displayCount = parseInt(count) || 0;
        if (displayCount <= 0) {
            displayCount = 0;
        }
        $('#notification_count').text(displayCount);
        $('#header_notif_badge').text(displayCount);
    }

    function fetchProviderNotifications() {
        $.get({
            url: '{{ route('provider.get_updated_data') }}',
            dataType: 'json',
            success: function (response) {
                if (response && response.data) {
                    let data = response.data;
                    updateBellCount(data.notification_count);
                    if (data.message !== undefined) {
                        $('#message_count').text(data.message);
                    }
                    if (data.notification_template) {
                        $('#show-notification-list').html(data.notification_template);
                    }
                }
            }
        });
    }

    $(document).ready(function() {
        fetchProviderNotifications();
    });

    $(document).on('click', '.notification-icon', function () {
        fetchProviderNotifications();
    });

    // 1. Mark single notification as read & track click on clicking item
    $(document).on('click', '.provider-notif-item', function () {
        let notifId = $(this).data('notif-id');
        if (notifId) {
            $.ajax({
                url: '{{ route('provider.notifications.mark-read') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    notification_id: notifId,
                    track_click: 1
                },
                success: function(resp) {
                    if (resp && resp.unread_count !== undefined) {
                        updateBellCount(resp.unread_count);
                    }
                }
            });
        }
    });

    // 2. Mark single notification as read via button
    $(document).on('click', '.btn-mark-read', function (e) {
        e.preventDefault();
        e.stopPropagation();
        let btn = $(this);
        let notifId = btn.data('notif-id');
        let row = btn.closest('.notification-row-item');

        if (notifId) {
            $.ajax({
                url: '{{ route('provider.notifications.mark-read') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    notification_id: notifId
                },
                success: function(resp) {
                    if (resp && resp.status === 'success') {
                        updateBellCount(resp.unread_count);
                        row.find('.unread-dot').remove();
                        row.find('.status-pill-unread').replaceWith('<span class="badge bg-light text-muted border fz-10 py-1 px-2 status-pill-read">{{ translate("Read") }}</span>');
                        row.find('.card-title').removeClass('fw-bold text-primary').addClass('fw-semibold');
                        btn.remove();
                        row.removeClass('bg-light bg-light-blue').css('background-color', 'transparent');
                    }
                }
            });
        }
    });

    // 3. Mark all notifications as read
    $(document).on('click', '.btn-mark-all-read', function (e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route('provider.notifications.mark-all-read') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(resp) {
                if (resp && resp.status === 'success') {
                    updateBellCount(0);
                    $('.unread-dot').remove();
                    $('.status-pill-unread').replaceWith('<span class="badge bg-light text-muted border fz-10 py-1 px-2 status-pill-read">{{ translate("Read") }}</span>');
                    $('.card-title').removeClass('fw-bold text-primary').addClass('fw-semibold');
                    $('.btn-mark-read').remove();
                    $('.notification-row-item').removeClass('bg-light bg-light-blue').css('background-color', 'transparent');
                    if (typeof toastr !== 'undefined') {
                        toastr.success('{{ translate("All notifications marked as read") }}');
                    }
                }
            }
        });
    });

    @php($fcm_credentials = business_config('firebase_message_config', 'third_party')->live_values ?? [])
    @if(empty($fcm_credentials) || empty($fcm_credentials['apiKey']))
        @php($fcm_credentials = \Modules\BusinessSettingsModule\Entities\BusinessSettings::where('key_name', 'firebase_message_config')->first()?->live_values ?? [])
    @endif

    @if(!empty($fcm_credentials) && !empty($fcm_credentials['apiKey']))
        try {
            var firebaseConfig = {
                apiKey: "{{isset($fcm_credentials['apiKey']) ? $fcm_credentials['apiKey'] : ''}}",
                authDomain: "{{isset($fcm_credentials['authDomain']) ? $fcm_credentials['authDomain'] : ''}}",
                projectId: "{{isset($fcm_credentials['projectId']) ? $fcm_credentials['projectId'] : ''}}",
                storageBucket: "{{isset($fcm_credentials['storageBucket']) ? $fcm_credentials['storageBucket'] : ''}}",
                messagingSenderId: "{{isset($fcm_credentials['messagingSenderId']) ? $fcm_credentials['messagingSenderId'] : ''}}",
                appId: "{{isset($fcm_credentials['appId']) ? $fcm_credentials['appId'] : ''}}",
                measurementId: "{{isset($fcm_credentials['measurementId']) ? $fcm_credentials['measurementId'] : ''}}"
            };
            firebase.initializeApp(firebaseConfig);
            const messaging = firebase.messaging();

            let appBase = '';
            if (window.location.pathname.indexOf('/mmc') === 0) {
                appBase = '/mmc';
            } else if (window.location.pathname.indexOf('/backend') === 0) {
                appBase = '/backend';
            }
            let swPath = appBase ? (appBase + '/firebase-messaging-sw.js') : '{{ url('firebase-messaging-sw.js') }}';
            let subscribeUrl = appBase ? (appBase + '/provider/subscribeToTopic') : '{{ route('provider.subscribeToTopic') }}';

            async function startFCM() {
                try {
                    let permission = Notification.permission;
                    if (permission === 'default') {
                        permission = await Notification.requestPermission();
                    }

                    if (permission !== 'granted') {
                        return;
                    }

                    let registration = null;
                    if ('serviceWorker' in navigator) {
                        try {
                            registration = await navigator.serviceWorker.register(swPath);
                            await navigator.serviceWorker.ready;
                            try {
                                messaging.useServiceWorker(registration);
                            } catch (e) {}
                        } catch (swErr) {
                            try {
                                registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
                                await navigator.serviceWorker.ready;
                                try {
                                    messaging.useServiceWorker(registration);
                                } catch (e) {}
                            } catch (swErr2) {
                                console.error("[FCM] Service Worker registration failed:", swErr2);
                            }
                        }
                    }

                    const token = registration
                        ? await messaging.getToken({ serviceWorkerRegistration: registration })
                        : await messaging.getToken();

                    if (!token) return;

                    subscribeTokenToBackend(token, 'demandium_provider_{{auth()->user()->provider->zone_id}}_{{ auth()->user()->provider->id }}_booking_message');
                    subscribeTokenToBackend(token, 'provider-admin-{{auth()->user()->provider->zone_id}}');
                    subscribeTokenToBackend(token, 'provider-admin-all');
                    @if($serviceAtProviderPlace)
                        @if(in_array('customer', $serviceLocations))
                            subscribeTokenToBackend(token, 'demandium_provider_{{auth()->user()->provider->zone_id}}_customer_booking_message');
                       @endif
                       @if(in_array('provider', $serviceLocations))
                            subscribeTokenToBackend(token, 'demandium_provider_{{auth()->user()->provider->zone_id}}_provider_booking_message');
                       @endif
                    @else
                       subscribeTokenToBackend(token, 'demandium_provider_{{auth()->user()->provider->zone_id}}_booking_message');
                    @endif
                } catch (error) {
                    console.error('[FCM] Error getting permission or token:', error);
                }
            }

            function subscribeTokenToBackend(token, topic) {
                fetch(subscribeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ token: token, topic: topic })
                }).catch(error => {
                    console.error('Subscription error:', error);
                });
            }
                    messaging.onMessage(function(payload) {
                        console.log("FCM Payload received:", payload);
                        playAudio();
                        fetchProviderNotifications();

                        let title = payload.notification?.title || payload.data?.title || '{{translate('New_Notification')}}';
                        let body = payload.notification?.body || payload.data?.body || payload.data?.description || "{{translate('You_have_new_booking_arrived')}}";
                        let notifType = payload.data?.type || payload.data?.notification_type || 'general';
                        let bookingId = payload.data?.booking_id;
                        let image = payload.data?.image || payload.notification?.image || null;
                        let notifId = payload.data?.notification_id || payload.notification?.notification_id || '';
                        let actionType = payload.data?.action_type || 'general';

                        function trackPushClick(id) {
                            if (id) {
                                $.ajax({
                                    url: '{{ route('push-notification.track-click') }}',
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        notification_id: id
                                    },
                                    success: function(resp) {
                                        console.log('Push click recorded:', resp);
                                    }
                                });
                            }
                        }

                        if (window.Notification && Notification.permission === 'granted') {
                            try {
                                new Notification(title, {
                                    body: body,
                                    icon: image || '{{ asset('public/assets/admin-module/img/placeholder.png') }}'
                                });
                            } catch (e) {}
                        }

                        if (bookingId || notifType === 'booking') {
                            Swal.fire({
                                title: title,
                                text: body,
                                type: 'info',
                                showCloseButton: true,
                                showCancelButton: true,
                                focusConfirm: false,
                                confirmButtonText: '{{translate('Show_Bookings')}}',
                                cancelButtonText: '{{translate('Close')}}',
                                confirmButtonColor: 'var(--bs-primary)',
                            }).then((result) => {
                                playAudio(false);
                                if (result.value) {
                                    trackPushClick(notifId);
                                    if (typeof checkBooking === 'function') checkBooking();
                                    location.href = "{{ route('provider.booking.list') }}?booking_status=pending&service_type=all";
                                }
                            });
                        } else {
                            let imgHtml = image ? `<div class="mb-3 text-center"><img src="${image}" class="img-fluid rounded" style="max-height: 180px;" alt=""></div>` : '';
                            
                            let targetUrl = null;
                            let confirmBtnText = '{{translate('OK')}}';
                            let showCancel = false;

                            if (actionType === 'quote_form' || notifType === 'service_request' || notifType === 'service_request_message') {
                                targetUrl = "{{ route('provider.service.request-list') }}";
                                confirmBtnText = '{{translate('View Service Requests')}}';
                                showCancel = true;
                            } else if (actionType === 'service_page') {
                                targetUrl = "{{ route('provider.service.available') }}";
                                confirmBtnText = '{{translate('View Services')}}';
                                showCancel = true;
                            } else {
                                targetUrl = null;
                                confirmBtnText = '{{translate('OK')}}';
                                showCancel = false;
                            }

                            Swal.fire({
                                title: title,
                                html: `${imgHtml}<p class="mb-0 text-dark fz-14">${body}</p>`,
                                type: 'info',
                                showCloseButton: true,
                                showCancelButton: showCancel,
                                confirmButtonText: confirmBtnText,
                                cancelButtonText: '{{translate('Close')}}',
                                confirmButtonColor: 'var(--bs-primary)',
                                cancelButtonColor: '#6c757d',
                            }).then((result) => {
                                playAudio(false);
                                if (result.value) {
                                    trackPushClick(notifId);
                                    if (targetUrl) {
                                        setTimeout(function() {
                                            location.href = targetUrl;
                                        }, 150);
                                    }
                                }
                            });
                        }
                    });
                    startFCM();
                } catch (fcmErr) {
                    console.warn('FCM initialization skipped or failed:', fcmErr);
                }
            @else
                console.error("[FCM-DEBUG] FAILED: Firebase apiKey is MISSING in Database for firebase_message_config!", @json($fcm_credentials));
            @endif
        </script>

{!! Toastr::message() !!}

@if (session('warning'))
    <script>
        toastr.warning('{{ session('warning') }}');
    </script>
@endif

@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif


@stack('script')

</body>

</html>
