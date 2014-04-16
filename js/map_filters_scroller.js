jQuery(document).ready(function(){
	jQuery("#map-taxonomy-categoria-mapa-filters-list").cycle({
	    speed: 600,
	    manualSpeed: 100,
	    slides: ".filter-group-col",
	    fx: "carousel",
	    timeout: 0,
	    
	    carouselFluid: true,
	    next:"#filter-cycle-next",
	    prev:"#filter-cycle-prev"
	});
});