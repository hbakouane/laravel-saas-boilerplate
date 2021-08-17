@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Plans</div>
                    <div class="card-body">
                        <button type="button" class="btn btn-info text-light mb-2" data-toggle="modal" data-target="#addPlanModal">
                            Create a plan
                        </button>

                        @if($subscribed)
                            <div class="col-md-12 alert alert-success">
                                <strong>You are subscribed to the {{ $subscribed_plan }} plan.</strong>
                                <form method="POST" action="{{ route('subscription.cancel', ['planToCancel' => $subscribed_plan]) }}">
                                    @csrf
                                    <button class="btn btn-danger">Cancel your subscription</button>
                                </form>
                            </div>
                        @endif

                        @if(! $hasPaymentMethod)
                            <div class="alert alert-info">
                                <strong>You don't have a payment method, You cannot subscribe to a plan</strong>
                                <a href="{{ route('settings.index') }}">Add a payment method</a>.
                            </div>
                        @endif

                        <div class="row">
                            @forelse($plans as $plan)
                                <div class="col-md-4 text-center">
                                    <div class="card">
                                        <div class="card-body">
                                            <h1>{{ $plan->title }}</h1>
                                            <h3>{{ $plan->price . 'usd / ' . $plan->interval }}</h3>
                                            <p>{{ $plan->description }}</p>
                                        </div>
                                        <div class="card-footer">
                                            <form class="d-inline-block" method="POST" action="{{ route('plans.destroy', ['stripe_plan_id' => $plan->stripe_plan_id]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger">Delete Plan</button>
                                            </form>
                                            <form class="d-inline-block" method="POST" action="{{ route('plans.subscribe', ['plan_id' => $plan->id]) }}">
                                                @csrf
                                                <button @if($subscribed OR ! $hasPaymentMethod) disabled @endif class="btn btn-success">Subscribe</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                            <p class="text-muted">No plans to show.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add a Plan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('plans.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" name="price" id="price" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="interval">Interval</label>
                        <select id="interval" name="interval" class="form-control">
                            <option value="day">Daily</option>
                            <option value="week">Weekly</option>
                            <option value="month" selected>Monthly</option>
                            <option value="year">Yearly</option>
                        </select>
                    </div>
                    <button class="btn btn-primary">Save</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>
@endsection