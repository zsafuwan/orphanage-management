/* Combine regional Google Places searches on one nationwide map. */
(function () {
    'use strict';
    const canvas = document.getElementById('malaysia_places_map');
    if (!canvas) return;
    const status = document.getElementById('orphanage_map_status');
    const nearButton = document.getElementById('btn_locate_user');
    const allButton = document.querySelector('#state_filter_tabs .state-tab');
    const regions = ['Kuala Lumpur', 'Selangor', 'Putrajaya', 'Johor', 'Kedah',
        'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang', 'Penang', 'Perak',
        'Perlis', 'Sabah', 'Sarawak', 'Terengganu', 'Labuan'];
    const placesById = new Map();
    let map;
    let popup;
    let completed = 0;
    let failed = 0;
    let userMarker;
    nearButton.disabled = true;
    function report() {
        status.textContent = placesById.size + ' unique Google Maps locations loaded · '
            + completed + '/32 regional searches completed'
            + (failed ? ' · ' + failed + ' searches failed' : '')
            + '. Google search coverage is limited; this is not a complete registry.';
    }
    function overview() {
        const bounds = new google.maps.LatLngBounds({lat: 0.85, lng: 99.5}, {lat: 7.5, lng: 119.5});
        placesById.forEach(place => bounds.extend(place.location));
        map.fitBounds(bounds, 35);
        allButton.classList.add('active');
        allButton.setAttribute('aria-pressed', 'true');
        document.getElementById('nearest_hub_name').textContent = 'Orphanages across Malaysia';
        document.getElementById('nearest_hub_dist').textContent = placesById.size + ' locations loaded';
        document.getElementById('nearest_hub_dir').href = 'https://www.google.com/maps/search/?api=1&query=orphanages+in+Malaysia';
    }
    function select(place, marker) {
        const content = document.createElement('div');
        const title = document.createElement('strong');
        title.textContent = place.displayName;
        const address = document.createElement('p');
        address.textContent = place.formattedAddress || '';
        const link = document.createElement('a');
        link.textContent = 'View on Google Maps';
        link.href = 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(place.displayName) + '&query_place_id=' + encodeURIComponent(place.id);
        link.target = '_blank';
        link.rel = 'noopener noreferrer';
        content.append(title, address, link);
        popup.setContent(content);
        popup.open({map, anchor: marker});
        document.getElementById('nearest_hub_name').textContent = place.displayName;
        document.getElementById('nearest_hub_dist').textContent = place.formattedAddress || '';
        document.getElementById('nearest_hub_dir').href = link.href;
    }
    function add(place, AdvancedMarkerElement) {
        if (!place.id || !place.location || placesById.has(place.id)) return;
        // Regional query text alone can return overseas matches.
        const country = (place.addressComponents || []).find(part => part.types.includes('country'));
        if (!country || country.shortText !== 'MY') return;
        const marker = new AdvancedMarkerElement({map, position: place.location, title: place.displayName});
        marker.addListener('click', () => select(place, marker));
        placesById.set(place.id, place);
    }
    function fail() {
        status.textContent = 'Nationwide locations could not load. Open Google Maps using the button below, or try again later.';
        nearButton.disabled = true;
    }
    window.hopeHavenPlacesReady = async function () {
        try {
            const {Map: GoogleMap, InfoWindow} = await google.maps.importLibrary('maps');
            const {AdvancedMarkerElement} = await google.maps.importLibrary('marker');
            const {Place} = await google.maps.importLibrary('places');
            map = new GoogleMap(canvas, {center: {lat: 4.2, lng: 109.5}, zoom: 5,
                mapId: 'DEMO_MAP_ID', streetViewControl: false, mapTypeControl: false});
            popup = new InfoWindow();
            overview();
            allButton.addEventListener('click', overview);
            // Sequential requests bound concurrency. Search both common languages.
            for (const region of regions) {
                for (const term of ['orphanages', 'rumah anak yatim']) {
                    try {
                        const result = await Place.searchByText({
                            textQuery: term + ' in ' + region + ', Malaysia',
                            fields: ['id', 'displayName', 'location', 'formattedAddress', 'addressComponents'],
                            maxResultCount: 20,
                            region: 'my',
                            locationRestriction: {south: 0.85, west: 99.5, north: 7.5, east: 119.5}
                        });
                        (result.places || []).forEach(place => add(place, AdvancedMarkerElement));
                    } catch (_) { failed++; }
                    completed++;
                    report();
                }
            }
            overview();
            nearButton.disabled = false;
            if (!placesById.size) {
                status.textContent = 'No locations loaded. Google Places may be unavailable or the API configuration may need updating. Use Google Maps below.';
            }
            nearButton.addEventListener('click', function () {
                if (!navigator.geolocation) { status.textContent = 'Location is unavailable. Use All Malaysia.'; return; }
                nearButton.disabled = true;
                navigator.geolocation.getCurrentPosition(position => {
                    const point = {lat: position.coords.latitude, lng: position.coords.longitude};
                    if (userMarker) userMarker.map = null;
                    userMarker = new AdvancedMarkerElement({map, position: point, title: 'Your location'});
                    map.setCenter(point);
                    map.setZoom(11);
                    allButton.classList.remove('active');
                    allButton.setAttribute('aria-pressed', 'false');
                    status.textContent = 'Showing your area. All ' + placesById.size + ' loaded locations remain on the map.';
                    nearButton.disabled = false;
                }, () => { nearButton.disabled = false; status.textContent = 'Could not access your location. Use All Malaysia.'; }, {timeout: 10000});
            });
        } catch (_) { fail(); }
    };
    window.gm_authFailure = fail;
    const script = document.createElement('script');
    const url = new URL('https://maps.googleapis.com/maps/api/js');
    url.searchParams.set('key', canvas.dataset.apiKey);
    url.searchParams.set('callback', 'hopeHavenPlacesReady');
    url.searchParams.set('loading', 'async');
    url.searchParams.set('v', 'weekly');
    script.src = url.href;
    script.async = true;
    script.onerror = fail;
    document.head.appendChild(script);
})();
