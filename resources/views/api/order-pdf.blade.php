<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>Lux Order Form</title>

    <style>
        .bg-dark-new {
            background-color: #323639;
            box-shadow: 1px 1px 10px 1px #000000;
            z-index: 1;
        }
        .border {
            border: 2px solid #000 !important;
        }
    </style>
</head>
<body class="bg-dark">

    @if ($orderData)
        <nav class="navbar bg-dark-new">
            <div class="container">
                <div class="w-100">
                <div class="row">
                    <div class="col-6">
                        <h5 class="text-light mb-0 mt-2">Order Form</h5>
                    </div>
                    <div class="col-6 text-end">
                        <button id='print-btn' onclick='printDiv();' class="btn btn-primary">
                            Print
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        </button>
                    </div>
                </div>
                </div>
            </div>
        </nav>

        <div class="container">
            <div class="card rounded-0">
                <div class="card-body">
                    <div id="DivIdToPrint">
                        <div class="mb-3" style="border:2px solid #000; padding: 0px 27px;">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <h3 style="margin: 28px 0;">Order Form</h3>
                                </div>
                            </div>

                            <div class="row align-items-center">
                                <div class="col-6">
                                    <div style="padding:0 15px;">
                                        <h4 style="font-weight: 500;">Lux Industries Limited</h4>
                                        <p style="margin-bottom:4px;">17th floor, North Wing</p>
                                        <p style="margin-bottom:4px;">Adventz Infinity</p>
                                        <p style="margin-bottom:4px;">BN - 5, Sector V</p>
                                        <p>Kolkata - 700091, W.B., India</p>
                                    </div>
                                </div>

                                @php
                                    $order_id = $id;
                                    $data = \App\Models\Order::findOrFail($order_id);
                                @endphp

                                <div class="col-6">
                                    <div style="padding: 0 15px 14px; border-left:2px solid #000;">
                                        <p><strong>Order no./ Date:</strong> <u>{{$data->order_no}}/ {{date('d.m.Y', strtotime($data->created_at))}}</u></p>
                                        <p><strong>Print Date:</strong> <u>{{date('d.m.Y')}}</u></p>
                                        <p><strong>From:</strong></p>
                                        <p><strong>M/S: </strong> <u>{{$data->stores ? $data->stores->name : ''}}</u></p>
                                        @if ($data->stores)
                                            <p><u>{{ $data->stores->address.' '.$data->stores->areas->name.' '.$data->stores->states->name.' '.$data->stores->areas->name.' '.$data->stores->pin }}</u></p>
                                            <p><strong>Booking Place:</strong> <u>{{ $data->stores->areas->name ? $data->stores->areas->name : $data->stores->areas->name }}</u></p>
                                        @endif
                                        <p style="margin-bottom:0;"><strong>Agent:</strong> <u>{{$data->users ? $data->users->name : ''}}</u></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                      
                        <div class="table-responsive">
                            <table class="table table-sm" style="border-left:2px solid #000; border-bottom:2px solid #000;">
                                <thead>
                                    @if(count(orderProductsUpdatedMatrix($orderData)) > 0)
                                    <tr>
                                    <th style="font-size: 13px; min-width:200px; border-top:2px solid #000; width: 242px;">Name of Quality Shape & Unit</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">75</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">80</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">85</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">90</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">95</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">100</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">105</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">110</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">115</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">120</th>
                                        
                                        <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;border-right:2px solid #000;">Total</th>
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalCOunt = 0;
                                    @endphp
                                    @foreach(orderProductsUpdatedMatrix($orderData) as $productKey => $productValue)
                                    
                                    @php
									
                                        $totalCOunt += $productValue['total'];
                                    @endphp

                                    <tr>
                                        <td style="border-left:2px solid #000; border-bottom:2px solid #000;">
                                            <p class="small text-dark fw-bold mb-0">{{$productValue['product_name']}}</p>
                                            <p class="small text-dark fw-bold mb-0">{{$productValue['product_style_no']}}</p>
                                            <p class="small text-dark fw-bold mb-0">{{$color->name ?? ''}}</p>
                                        </td>
                                       <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['75'] ? $productValue['75'] : '' }}</p></td>
                                        <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['80'] ? $productValue['80'] : '' }}</p></td>
                                        <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['85'] ? $productValue['85'] : '' }}</p></td>
                                        <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['90'] ? $productValue['90'] : '' }}</p></td>
                                        <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['95'] ? $productValue['95'] : '' }}</p></td>
                                        <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['100'] ? $productValue['100'] : '' }}</p></td>
                                        <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['105'] ? $productValue['105'] : '' }}</p></td>
                                        <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['110'] ? $productValue['110'] : '' }}</p></td>
                                        <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['115'] ? $productValue['115'] : '' }}</p></td>
                                        <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['120'] ? $productValue['120'] : '' }}</p></td>
                                        
                                        <td style="border-left:2px solid #000; border-bottom:2px solid #000;border-right:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{$productValue['total']}}</p></td>
                                       
                                    </tr>
                                    @endforeach
                                    @endif
                                    @if(count(orderProductsUpdatedMatrixChild($orderData)) > 0)
                                    <thead>
                                        <tr>
                                            <th style="color: #6c757d; font-size: 13px; min-width:200px; border-bottom:2px solid #000; width: 242px;">Name of Quality Shape & Unit</th>
                                            <th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;">1-2</th>
                                            <th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;">2-3</th>
                                            <th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;">3-4</th>
                                            <th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;">5-6</th>
                                            <th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;">7-8</th>
                                            <th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;">9-10</th>
                                            <th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;">11-12</th>
                                            <th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;">13-14</th>
											 <th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;"></th>
                                            <th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;border-right:2px solid #000;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalProductCount = 0;
                                        @endphp
                                        @foreach(orderProductsUpdatedMatrixChild($orderData) as $productKey => $productOrderValue)
                                        @php
										 $color=\App\Models\Color::where('id',$productOrderValue['color'])->first();
                                            $totalCOunt += $productOrderValue['total'];
                                            $totalProductCount += $productOrderValue['total'];
                                            $tcount=$totalProductCount+$totalCOunt;
                                        @endphp
                                        <tr>
                                            <td style="; border-bottom:2px solid #000;">
                                                <p class="small text-dark fw-bold mb-0">{{$productOrderValue['product_name']}}</p>
                                                <p class="small text-dark fw-bold mb-0">{{$productOrderValue['product_style_no']}}</p>
                                                <p class="small text-dark fw-bold mb-0">{{$color->name}}</p>
                                            </td>
                                            <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productOrderValue['1-2'] ? $productOrderValue['1-2'] : '' }}</p></td>
                                            <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productOrderValue['2-3'] ? $productOrderValue['2-3'] : '' }}</p></td>
                                            <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productOrderValue['3-4'] ? $productOrderValue['3-4'] : '' }}</p></td>
                                            <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productOrderValue['5-6'] ? $productOrderValue['5-6'] : '' }}</p></td>
                                            <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productOrderValue['7-8'] ? $productOrderValue['7-8'] : '' }}</p></td>
                                            <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productOrderValue['9-10'] ? $productOrderValue['9-10'] : '' }}</p></td>
                                            <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productOrderValue['11-12'] ? $productOrderValue['11-12'] : '' }}</p></td>
                                            <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productOrderValue['13-14'] ? $productOrderValue['13-14'] : '' }}</p></td>
											<td style="border-left:2px solid #000; border-bottom:2px solid #000;"></td>
                                            <td style="border-left:2px solid #000; border-bottom:2px solid #000;border-right:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{$productOrderValue['total']}}</p></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    @endif
                                    <tbody>
                                        <tr>
                                            <td style="">
                                                <p class="small text-muted  mb-0">Total</p>
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
											<td></td>
											<td></td>
                                            <td style="border-left:2px solid #000; border-bottom:2px solid #000;border-right:2px solid #000;"><p class="small text-muted fw-bold mb-0">{{ $totalCOunt }}</p></td>
                                            {{-- <td style="border-left:2px solid #000;"><p class="small text-muted fw-bold mb-0">{{$cartData->sum('qty')}}</p></td> --}}
                                        </tr>
                                    </tbody>
                            </table>
                        </div>
                    </div>

         
                </div>
            </div>
        </div>
    @endif

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://jasonday.github.io/printThis/printThis.js"></script>

    <script>
        function printDiv() {
            $('#DivIdToPrint').printThis({
                importCSS: true,
                importStyle: true,
            });
        }
    </script>
</body>
</html>
