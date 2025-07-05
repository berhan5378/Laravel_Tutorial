 
    <div class="edit"> 
        <div>
            @if(isset($shipments) && count($shipments))
            <i class="ri-close-circle-line"></i>
            <div class="shipping-address">
                <h3>Shipping address</h3>
                @foreach ($shipments as $shipment)
                <div class="address-details">
                    <div class="address">
                        <input type="radio" name="" id="">
                        <div class="address-info">
                            <input type="text" name="" class="shipping-address-id" value="{{$shipment->id}}" hidden>
                            <p class="name paragraph">{{$shipment->contact_name}} &nbsp; <span class="phone-number">{{$shipment->contact_phone}}</span></p>
                            <p class="address">{{$shipment->address}}</p>
                            <p class="location">{{$shipment->sub_city.", ".$shipment->city.", ".$shipment->country.", ".$shipment->zip_code}}</p>
                        </div> 
                    </div>
                    <a href="{{ route('view.orderForm.edit', ['id' => $shipment->id]) }}" class="edit-btn-b-link">Edit</a>
                </div>
                @endforeach
            </div> 
            <button class="edit-btn-b add-btn">Add new address</button>
            @else
            <x-ecommerce.editForm /> 
            @endif
        </div>
    </div>   
<style>
 
    .edit{
       max-width: 600px;
       margin: 0 auto;
       display: grid; 
       align-items: center;
       height: 100vh;
    }
    .edit >div{ 
        position: relative;
        border-radius: 10px;
        padding-bottom: 10px; 
        background: #ffffff;
    }
  .edit >div .ri-close-circle-line{
    position: absolute;
    right: 1rem;
    top: 1rem;
    cursor: pointer;
  }
    .edit h3{
        text-align: center;  
        padding-bottom: .5rem;
    }
    .edit >div >button{
        background-color: #007BFF;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
        margin: 20px auto;
        display: block;
    }
    .edit >button:hover{
        background-color: #0056b3;
    }
    .edit .address{
        display: flex;
        align-items: center; 
        gap: 15px;
    }
    .edit .address-details{
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
    }
    .edit .shipping-address {
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px; 
        }
        .edit .shipping-address h3{ 
            margin-bottom: 10px;
            color: #333;
        }
        .edit .address-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 10px 0;
        }
        .edit .address-details  p{
            margin-bottom: 4px;
            color: #555;
            font-size: 14px;
        }
 
        .edit .address-details  p.paragraph{
            font-weight: 700;
            font-size: 14px;
            color: #333;
        }
        .edit .address-details .phone-number{
            font-weight: normal;
        }
        .edit .address-details  button{
            background-color: transparent;
            border: none;
            cursor: pointer;
            color: #007BFF;
            font-size: 1rem;
        }
</style>
</html>