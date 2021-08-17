@extends('layouts.app')

@section('content')
    <style>
        .hidden { display: none !important; }
    </style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Settings</div>
                    <div class="card-body">
                        @if(! $customer->hasPaymentMethod())
                            <form method="POST" action="{{ route('settings.update') }}">
                                @csrf
                                @method('PUT')
                                <div class="alert alert-danger hidden" id="errorAlert"></div>
                                <div class="alert alert-success hidden" id="successAlert"></div>
                                <input type="text" class="form-control mb-2" value="{{ $customer->name }}" name="card_holder_name" id="card-holder-name">
                                <!-- Stripe Elements Placeholder -->
                                <div id="card-element" class="border rounded p-2 mb-3"></div>
            
                                <button type="button" class="btn btn-info text-light" id="card-button" data-secret="{{ $intent->client_secret }}">
                                    Add Payment Method
                                </button>
                            </form>
                        @else
                            <p class="text-muted h4 mb-3">Payment method</p>
                            <p>Credit card: {{ $customer->pm_type }}</p>
                            <p>Credit card number: **** **** **** {{ $customer->pm_last_four }}</p>
                        @endif
                    </div>
                    @if($customer->hasPaymentMethod())
                    <div class="card-footer">
                        <form method="POST" action="{{ route('settings.deletePaymentMethod', ['customer_id' => $customer->id]) }}">
                            @csrf
                            @method('PUT')
                            <button class="btn btn-danger">Delete my payment method</button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe("{{ env('STRIPE_KEY') }}");

    const elements = stripe.elements();
    const cardElement = elements.create('card', { hidePostalCode: true });

    cardElement.mount('#card-element');

    const cardHolderName = document.getElementById('card-holder-name');
    const cardButton = document.getElementById('card-button');
    let errorAlert = document.querySelector('#errorAlert')
    let successAlert = document.querySelector('#successAlert')

    successAlert.classList.add('hidden')
    errorAlert.classList.add('hidden')

    cardButton.addEventListener('click', async (e) => {
        const { paymentMethod, error } = await stripe.createPaymentMethod(
            'card', cardElement
        );
        if (error) {
            successAlert.classList.add('hidden')
            errorAlert.classList.remove('hidden')
            errorAlert.innerHTML = error.message
        } else {
            errorAlert.classList.add('hidden')
            successAlert.classList.remove('hidden')
            successAlert.innerHTML = 'Processing ...'
            fetch("{{ route('settings.update') }}", {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    _token: '{{ csrf_token() }}',
                    paymentMethod: paymentMethod
                })
            })
            .then(res => successAlert.innerHTML = "Payment method added successfully")
            .catch(err => alert("ERROR"))
        }
    });
</script>
@endsection