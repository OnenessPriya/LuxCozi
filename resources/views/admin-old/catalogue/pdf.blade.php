@extends('admin.layouts.app')
@section('page', 'Catalogue')
	
	<style>
		body {
			background: #525659;
		}
		
		.pdf_list {
			margin: 0;
			padding: 100px 0 0;
			list-style-type: none;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
		}
		.pdf_list li {
			display: block;
			margin-bottom: 20px;
			text-align: center;
		}
		.pdf_list li img {
			max-width: 100%;
			max-height: 900px;
		}
		
		.pdf_list_small {
			margin: 0;
			padding: 100px 0 0;
			list-style-type: none;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
		}
		.pdf_list_small li {
			display: block;
			margin-bottom: 20px;
			text-align: center;
		}
		.pdf_list_small li img {
			max-width: 100%;
			max-height: 200px;
		}
		.pdf_sidebar {
			background: #313538;
			position: fixed;
			top: 0;
			left: 0;
			height: 100%;
			overflow: auto;
			width: 300px;
		}
		.content_area {
			width: 100%;
		}
		
		@media(max-width: 575px) {
			.pdf_sidebar {
				display: none;
			}
			.content_area {
				padding-left: 0;
			}
			.pdf_list li img {
				max-width: 90%;
			}
		}
	</style>


@section('content')

		
				
			
			<div class="content_area">
				<ul class="pdf_list">
					@foreach($image as $item)
					<li id="{{$item->id}}">
						<img src="{{asset($item->image)}}">
					</li>
					@endforeach
					{{--<li id="page2">
						<img src="https://onnb2b.in/uploads/catalogue/cat_image/UK CLASSIC CATALOGUE_compressed (1)_page-0023.jpg">
					</li>
					<li id="page3">
						<img src="https://onnb2b.in/uploads/catalogue/cat_image/UK CLASSIC CATALOGUE_compressed (1)_page-0022.jpg">
					</li>
					<li id="page4">
						<img src="https://onnb2b.in/uploads/catalogue/cat_image/UK CLASSIC CATALOGUE_compressed (1)_page-0021.jpg">
					</li> --}}
				</ul>
			</div>
		
@endsection