var galleryInitialization = function(){
  var initializeGallery= (function(){

      function init(opts) {
        $gallery = $(opts.gallery);
        galleryClassName = opts.gallery;
        $galleryNavigation = $(opts.galleryNavigation);
        $heroGallery = $(opts.heroGallery);
        $brandListGallery = $(opts.brandListGallery);
        handleGalleryInitialization();
      }

      function handleGalleryInitialization(){

        $gallery.flickity({
          pageDots:false
        });

        $galleryNavigation.flickity({
          asNavFor: galleryClassName,
          contain: true,
          pageDots: false,
          prevNextButtons: false,
          lazyLoad: true
        });

        $heroGallery.flickity({
            arrowShape: {
              x0: 15,
              x1: 55, y1: 30,
              x2: 70, y2: 40,
              x3: 45
            },
            contain: true,
            pageDots: false
        });

        $brandListGallery.flickity({
          contain: true,
          pageDots: false,
          prevNextButtons: false
        });
      }

      var $gallery,
          $galleryNavigation,
          galleryClassName,
          $heroGallery,
          $brandListGallery;

      var publicAPI = {
        init: init
      };

      return publicAPI;
  })();
  
  initializeGallery.init({
    gallery: '.js-p-product-gallery',
    galleryNavigation: '.js-p-product-gallery-navigation',
    heroGallery: '.js-gal',
    brandListGallery: '.js-brand-gal'
  });
  
}
