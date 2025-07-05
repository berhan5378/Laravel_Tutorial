    <i class="ri-close-circle-line"></i>
       <h3>Add new address</h3>
        <form action="" method="post">
            <label>Country/region</label>
            <select name="country" required>
              <option selected>Ethiopia</option>
                <option>China</option>
                <option>USA</option>
            </select>
            <label>Contact information</label>
            <div class="row">
                <input type="text" name="contact_name" placeholder="Contact name*" value="{{old('contact_name', $shipment->contact_name ?? '')}}"  required>
                <input type="tel" name="contact_phone" placeholder="Phone number*" value="{{old('contact_phone', $shipment->contact_phone ?? '')}}" required>
            </div>
            <label>Address</label>
            <div class="row">
                <input type="text" name="address"  placeholder="Street, house/apartment/unit*" value="{{old('address', $shipment->address ?? '')}}" required />
                <input type="text" name="address2" placeholder="Apt, suite, unit, etc (optional)" value="{{old('address2', $shipment->address2 ?? '')}}" />
            </div>
            <div class="row">
                <input type="text" name="sub_city" placeholder="Sub-city*" value="{{old('sub_city', $shipment->sub_city ?? '')}}" required />
                <input type="text" name="city" placeholder="City*"  value="{{old('city', $shipment->city ?? '')}}" required />
                <input type="number" name="zip_code" placeholder="ZIP code*" value="{{old('zip_code', $shipment->zip_code ?? '')}}" required />
            </div>
            <div class="buttons">
                <button class="confirm-btn" data-shipmentId="{{ $shipment->id ?? '' }}">Confirm</button>
                <button class="cancel-btn">Cancel</button>
            </div>
        </form> 
<style>
 
    .edit > div > h3 {
        padding-top: 20px;
    }
    .edit h3{
        text-align: center;  
        padding-bottom: .5rem;
    }
    .edit form {
            padding: 20px; 
            border-radius: 8px; 
            display: grid;
    }
    .edit label {
            margin-bottom: 10px; 
            font-weight: bold;
        }
    .edit input, .edit select {
            width: 100%; 
            padding: 10px; 
            margin-bottom: 15px; 
            border: 1px solid #ccc; 
            border-radius: 4px;
        }
    select {
        width: 230px;
        }  
    .row {
            display: flex; 
            flex: 0 0 50%;
            gap: 15px;
        }  
    .edit .buttons {
            display: flex; 
            gap: 1rem; 
        }
    .edit .confirm-btn, .cancel-btn {
            min-width: 150px;
            padding: 10px 20px; 
            border: none; 
            border-radius:20px; 
            cursor: pointer;
        }
    .edit .confirm-btn {
            background-color: #007BFF; 
            color: #fff;
        }
    .edit .confirm-btn:hover {
            background-color: #0056b3; 
        }
    .edit .cancel-btn {
            background-color: #f44336; 
            color: #fff;
        }
    .edit .cancel-btn:hover {
            background-color: #d32f2f; 
        }
@media (max-width: 400px) {
 
    form{
        gap: 15px;
    }
        .row {
            flex-direction: column; 
            gap: 10px;
        }
 
        input, select {
            margin: 0; 
        }
        .confirm-btn, .cancel-btn {
            min-width: 100px; 
        }
    }
</style>
</html>