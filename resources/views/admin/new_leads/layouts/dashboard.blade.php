 <style> 

    .house-block {
      background: #bfbfbf
      margin-bottom: 30px;
      border-radius: 4px;
      padding: 10px;
      width: 100%;
    }

    .house-header {
      display: flex;
      align-items: center;
      padding: 10px 0;
      cursor: pointer;
    }

    .house-info {
      flex: 1;
    }

    .house-info .title {
      font-weight: bold;
      color: #65a300;
      margin-bottom: 5px;
    }

    .house-info .address {
      font-size: 14px;
      color: #444;
    }

    .house-img img {
      width: 100px;
      height: 75px;
      object-fit: cover;
      border-radius: 3px;
    }

    .entry-row {
      display: flex;
      align-items: center;
      background: #f8f8f8;
      padding: 12px 15px;
      margin-top: 5px;
      font-size: 15px;
      border-bottom: 2px solid #ccc;
    }

    .icon {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      background: #a6ce39;
      color: #fff;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      margin-right: 20px;
      flex-shrink: 0;
    }

    .entry-col {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .entry-col .label {
      font-weight: bold;
      color: #555;
      margin-bottom: 3px;
    }

    .view-icon {
      width: 45px;
      height: 45px;
      background: #a6ce39;
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-left: auto;
    }
    .view-icon i {
      font-size:20px;
    }

    .view-icon:hover {
      background:rgb(206, 107, 57);
    }

    .house-details {
      display: block !important;
    }

    #phaseModal .modal-body {
      padding:10px;
    }

    #phaseModal .card-header{
      padding:10px !important;
    }


   
  </style>
 


<div class="dashboard scroll-wrapper" data-id="{{$customer->id}}" id="dashboard" >
   
</div>
