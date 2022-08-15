<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    

    <title>bKash Payment System in Laravel 9</title>
  </head>
  <body>
    <div class="container">
        <h3>Here Your Order Details</h3>
        <div class="card" style="width: 18rem;">
            <div class="card-body">
              <h5 class="card-title">{{ $order->product_name }}</h5>
              <p class="card-text invoice">{{ $order->invoice }}</p>
              <p class="card-text amount">{{ $order->amount }}</p>
              @if($order->status == 'Pending')
                <a href="#" class="card-link btn btn-danger">Pay With bKash</a>
              @else 
                <a href="#" class="card-link btn btn-success">Paid</a>
              @endif
              <a href="{{ route('orders.index') }}" class="card-link btn btn-success">Go Back</a>
            </div>
          </div>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.8.3/jquery.min.js" integrity="sha512-J9QfbPuFlqGD2CYVCa6zn8/7PEgZnGpM5qtFOBZgwujjDnG5w5Fjx46YzqvIh/ORstcj7luStvvIHkisQi5SKw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script id = "myScript" src="https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js"></script>
 

  

    <script>
        $(document).ready(function(){
            var accessToken='';
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{!! route('token') !!}",
                type: 'POST',
                contentType: 'application/json',
                success: function (data) {
                    console.log('got data from token  ..');
                    console.log(JSON.stringify(data));
                    
                    accessToken=JSON.stringify(data);
                },
                error: function(){
                            console.log('error');
                            
                }
            });
            var paymentConfig={
                createCheckoutURL:"{!! route('createpayment') !!}",
                executeCheckoutURL:"{!! route('executepayment') !!}",
            };
            
            var paymentRequest;
            paymentRequest = { amount: $('.amount').text(),intent:'sale', invoice: $('.invoice').text() };
                console.log(JSON.stringify(paymentRequest));
            
            bKash.init({
                paymentMode: 'checkout',
                paymentRequest: paymentRequest,
                createRequest: function(request){
                    console.log('=> createRequest (request) :: ');
                    console.log(request);
                    
                    $.ajax({
                        url: paymentConfig.createCheckoutURL+"?amount="+paymentRequest.amount+'&invoice='+paymentRequest.invoice,
                        type:'GET',
                        contentType: 'application/json',
                        success: function(data) {
                            console.log('got data from create  ..');
                            console.log('data ::=>');
                            console.log(JSON.stringify(data));
                            
                            var obj = JSON.parse(data);
                            
                            if(data && obj.paymentID != null){
                                paymentID = obj.paymentID;
                                bKash.create().onSuccess(obj);
                            }
                            else {
                                console.log('error');
                                bKash.create().onError();
                            }
                        },
                        error: function(){
                            console.log('error');
                            bKash.create().onError();
                        }
                    });
                },
                
                executeRequestOnAuthorization: function(){
                    console.log('=> executeRequestOnAuthorization');
                    $.ajax({
                        url: paymentConfig.executeCheckoutURL+"?paymentID="+paymentID,
                        type: 'GET',
                        contentType:'application/json',
                        success: function(data){
                            console.log('got data from execute  ..');
                            console.log('data ::=>');
                            console.log(JSON.stringify(data));
                            
                            data = JSON.parse(data);
                            if(data && data.paymentID != null){
                                alert('[SUCCESS] data : ' + JSON.stringify(data));
                                //  Redirect After user done payment
                                window.location.href = "{!! route('orders.index') !!}";                              
                            }
                            else {
                                bKash.execute().onError();
                            }
                        },
                        error: function(){
                            bKash.execute().onError();
                        }
                    });
                }
            });
            
            console.log("Right after init ");
        
            
        });
	
        function callReconfigure(val){
            bKash.reconfigure(val);
        }
        function clickPayButton(){
            $("#bKash_button").trigger('click');
        }


          

            
    </script>
  </body>
</html>