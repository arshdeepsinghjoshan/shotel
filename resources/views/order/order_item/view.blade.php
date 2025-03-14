@extends('layouts.master')
@section('content')
<?php

use App\Models\User;
?>

@php
 $productModelJson = json_decode($model->product_json);

@endphp
<x-a-breadcrumb :columns="[
        [
            'url' => '/',
            'label' => 'Home',
        ],
        [
             'url' => 'order',
            'label' => 'Orders',
        ],
        !empty($productModelJson && $productModelJson->name) ? (strlen($productModelJson->name) > 100 ? substr($productModelJson->name, 0, 100) . '...' : $productModelJson->name) : 'N/A'
    ]" />

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-body">
                    <h5>{{ !empty($productModelJson && $productModelJson->name) ? (strlen($productModelJson->name) > 100 ? substr($productModelJson->name, 0, 100) . '...' : $productModelJson->name) : 'N/A' }}
                        <span class="{{ $model->getStateBadgeOption() }}">{{ $model->getState() }}</span>
                    </h5>

                    <x-a-detail-view :model="$model" :type="'double'" :column="
    [
        'id',
        'quantity',
     
    [
        'attribute'=> 'total_amount',
        'value'=>number_format($model->unit_amount, 2)
     ],
     [
        'attribute'=> 'unit_amount',
        'value'=>number_format($model->unit_amount, 2)
     ],
     [
        'attribute' => 'created_at',
        'label' => 'Created at',
        'value' => (empty($model->created_at)) ? 'N/A' : date('Y-m-d h:i:s A', strtotime($model->created_at)),
     ],
     [
        'attribute' => 'created_at',
        'label' => 'Created at',
        'value' => (empty($model->created_at)) ? 'N/A' : date('Y-m-d h:i:s A', strtotime($model->created_at)),
     ],
     [
        'attribute' => 'created_by_id',
        'label' => 'Created By',
        'value' => !empty($model->createdBy && $model->createdBy->name) ? $model->createdBy->name : 'N/A',
     ]
    
    ]
    " />
    <p class="mt-3">


    </p>
                </div>
            </div>
            @if($model->images && count(json_decode($model->images)) > 0)
            <div class="col-lg-12 mt-5">
                <div class="card">
                    <div class="card-header">
                        <h3>Product Bill Images</h3>
                    </div>
                    <div class="card-body">
                        <div class="preview-images" style="display: flex; overflow-x: auto; gap: 10px;">
                            @foreach(json_decode($model->images) as $image)
                            <div class="preview-image">
                                <img class="zoom-image" src="{{ asset('/products/'.$image) }}" data-id="{{$image}}" alt="" width="80px" height="100px">
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>

    </div>
    <style>
        /* Modal styles */
        .modal {
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            overflow: auto;
        }

        .modal-content {
            display: block;
            max-width: 90%;
            margin: auto;
            max-height: 80%;
            margin-top: 10px;
        }

        .close {
            position: absolute;
            top: 20px;
            right: 25px;
            color: #f1f1f1;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    <div id="imageModal" class="modal" style="display:none;">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImage">
        <div id="caption"></div>
    </div>

    <script>
        // Get all images with class 'zoom-image'
        const images = document.querySelectorAll('.zoom-image');
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const caption = document.getElementById('caption');

        // Add click event listener for each image
        images.forEach(image => {
            image.addEventListener('click', function() {
                modal.style.display = 'block';
                modalImage.src = this.src;
                caption.innerText = this.alt || 'Zoomed Image';
            });
        });

        // Close the modal
        function closeModal() {
            modal.style.display = 'none';
        }

        // Close modal if clicked outside of the image
        window.onclick = function(event) {
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>



</div>
@endsection