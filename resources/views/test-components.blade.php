@extends('layouts.app')
@section('title', 'Test Components - Bambudua')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">🧪 Test Komponen Bambudua</h2>
            
            {{-- Test Alert Component --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Alert Components</h5>
                </div>
                <div class="card-body">
                    <x-alert type="success" title="Success Alert" dismissible>
                        This is a success alert with auto-dismiss functionality.
                    </x-alert>
                    
                    <x-alert type="danger" title="Error Alert">
                        This is an error alert that stays visible.
                    </x-alert>
                    
                    <x-alert type="warning" dismissible>
                        Simple warning alert without title.
                    </x-alert>
                    
                    <x-alert type="info">
                        Info alert with custom icon and styling.
                    </x-alert>
                </div>
            </div>

            {{-- Test Form Components --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Form Components</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input 
                                    name="test_input" 
                                    label="Test Input" 
                                    placeholder="Enter some text..."
                                    required
                                    help="This is a helper text"
                                />
                                
                                <x-form.input 
                                    name="email_test" 
                                    type="email"
                                    label="Email Address" 
                                    :prepend="'<i class=\'ri-mail-line\'></i>'"
                                />
                                
                                <x-form.input 
                                    name="price_test" 
                                    type="number"
                                    label="Price" 
                                    :append="'Rp'"
                                />
                            </div>
                            <div class="col-md-6">
                                <x-form.select 
                                    name="test_select" 
                                    label="Test Select"
                                    placeholder="Choose an option..."
                                    :options="[
                                        '1' => 'Option 1',
                                        '2' => 'Option 2', 
                                        '3' => 'Option 3'
                                    ]"
                                    required
                                />
                                
                                <x-form.select 
                                    name="test_multiple" 
                                    label="Multiple Select"
                                    :options="['A', 'B', 'C', 'D']"
                                    multiple
                                />
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Test Search Component --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Advanced Search Component</h5>
                </div>
                <div class="card-body">
                    <x-search.advanced 
                        placeholder="Search patients, RM, KTP..."
                        name="search_test"
                        :filters="[
                            [
                                'name' => 'status',
                                'type' => 'select',
                                'label' => 'Status',
                                'options' => [
                                    'active' => 'Active',
                                    'inactive' => 'Inactive'
                                ]
                            ],
                            [
                                'name' => 'date',
                                'type' => 'date',
                                'label' => 'Date'
                            ]
                        ]"
                        result-container="search-results"
                    />
                    
                    <div id="search-results" class="mt-3">
                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            Search results will appear here
                        </div>
                    </div>
                </div>
            </div>

            {{-- Test Export Buttons --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Export Components</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Multiple Export Options:</h6>
                        <x-export.buttons 
                            pdf-url="#pdf"
                            excel-url="#excel"
                            csv-url="#csv"
                            title="Test Data"
                        />
                    </div>
                    
                    <div class="mb-3">
                        <h6>Single Export:</h6>
                        <x-export.buttons 
                            pdf-url="#single-pdf"
                            title="PDF Report"
                        />
                    </div>
                </div>
            </div>

            {{-- Test Loading Component --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Loading & Utility Functions</h5>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary me-2" onclick="testLoading()">
                        <i class="ri-loader-line"></i>
                        <span class="btn-text">Test Button Loading</span>
                        <span class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                    
                    <button class="btn btn-info me-2" onclick="testOverlayLoading()">
                        Test Overlay Loading
                    </button>
                    
                    <button class="btn btn-success me-2" onclick="testToast()">
                        Test Toast
                    </button>
                    
                    <button class="btn btn-warning me-2" onclick="testConfirm()">
                        Test Confirmation
                    </button>
                    
                    <button class="btn btn-danger" onclick="testError()">
                        Test Error
                    </button>
                </div>
            </div>

            {{-- Test Modal Component --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Modal Component</h5>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#testModal">
                        Open Test Modal
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Test Modal --}}
<x-modal 
    id="testModal" 
    title="Test Modal" 
    icon="ri-test-tube-line"
    size="modal-lg"
    scrollable>
    <p>This is a test modal using the new modal component.</p>
    <p>It includes:</p>
    <ul>
        <li>Custom title with icon</li>
        <li>Scrollable content</li>
        <li>Large size</li>
        <li>Default footer buttons</li>
    </ul>
    
    <x-slot name="footerButtons">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary">Save Changes</button>
    </x-slot>
</x-modal>

@include('components.loading', ['id' => 'test-loading'])

@endsection

@push('scripts')
<script>
// Test functions for utilities
function testLoading() {
    const originalText = BambuduaUtils.showButtonLoading('.btn-primary', 'Loading...');
    
    setTimeout(() => {
        BambuduaUtils.hideButtonLoading('.btn-primary', originalText);
    }, 3000);
}

function testOverlayLoading() {
    BambuduaUtils.showOverlayLoading('Testing overlay loading...');
    
    setTimeout(() => {
        BambuduaUtils.hideOverlayLoading();
    }, 2000);
}

function testToast() {
    BambuduaUtils.showToast('This is a test toast notification!', 'success');
    
    setTimeout(() => {
        BambuduaUtils.showToast('Another toast with error', 'error');
    }, 1000);
}

function testConfirm() {
    BambuduaUtils.confirmAction(
        'Test Confirmation',
        'Are you sure you want to test this confirmation dialog?'
    ).then((confirmed) => {
        if (confirmed) {
            BambuduaUtils.showToast('You confirmed!', 'success');
        } else {
            BambuduaUtils.showToast('You cancelled', 'info');
        }
    });
}

function testError() {
    // Simulate an error
    throw new Error('This is a test error to check error handling');
}

$(document).ready(function() {
    console.log('🧪 Component Test Page Ready');
    
    // Test auto-resize textarea
    BambuduaUtils.autoResizeTextarea('textarea[data-auto-resize]');
    
    // Show success message
    setTimeout(() => {
        BambuduaUtils.showToast('Component test page loaded successfully!', 'success');
    }, 1000);
});
</script>
@endpush