<div class="row portfolio-container" data-aos="fade-up" data-aos-delay="200">
    @foreach($products as $product)
        <div class="col-md-{{ $product->size }} portfolio-item filter-app">
            @if(!empty($product->media[0]) and $product->media[0]->type == 0)
                <img src="/images/{{ $product->media[0]->media_url }}" class="img-fluid full_image">
                <div class="portfolio-info">
                    <h4 class="text-center">{{ $product->name }}</h4>
                    <a href="/images/{{ $product->media[0]->media_url }}" data-gall="portfolioGallery" class="venobox preview-link" title="{{ $product->name }}"><i class="bx bx-plus"></i></a>
                    <a href="/product/details/{{ $product->id }}" class="details-link" title="More Details"><i class="bx bx-link"></i></a>
                </div>
            @endif
        </div>
    @endforeach
</div>