/**
 * Alpine.js component — Google Places address autocomplete
 * Usage: x-data="addressAutocomplete({rue:'rue', ville:'ville', codePostal:'code_postal', pays:null})"
 */
function addressAutocomplete(config) {
    return {
        _ac: null,

        init() {
            this._trySetup();
        },

        _trySetup() {
            if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                // Find the actual <input> inside this field wrapper
                const input = this.$el.querySelector('input[type="text"]');
                if (!input || this._ac) return;

                this._ac = new google.maps.places.Autocomplete(input, {
                    types: ['geocode'],
                    fields: ['address_components', 'geometry'],
                });

                this._ac.addListener('place_changed', () => {
                    const place = this._ac.getPlace();
                    if (!place.address_components) return;

                    let streetNumber = '', route = '', ville = '', codePostal = '', pays = '';

                    place.address_components.forEach(c => {
                        if (c.types.includes('street_number')) streetNumber = c.long_name;
                        if (c.types.includes('route'))         route        = c.long_name;
                        if (c.types.includes('locality'))      ville        = c.long_name;
                        if (c.types.includes('postal_code'))   codePostal   = c.long_name;
                        if (c.types.includes('country'))       pays         = c.long_name;
                    });

                    const rue = [streetNumber, route].filter(Boolean).join(' ');

                    if (config.rue)        this.$wire.set('data.' + config.rue,        rue);
                    if (config.ville)      this.$wire.set('data.' + config.ville,      ville);
                    if (config.codePostal) this.$wire.set('data.' + config.codePostal, codePostal);
                    if (config.pays)       this.$wire.set('data.' + config.pays,       pays);

                    // Coordonnées géographiques pour le timezone
                    if (place.geometry && place.geometry.location) {
                        if (config.lat) this.$wire.set('data.' + config.lat, place.geometry.location.lat());
                        if (config.lng) this.$wire.set('data.' + config.lng, place.geometry.location.lng());
                    }

                    // Vider le champ de recherche après sélection
                    input.value = '';
                });
            } else {
                // Google Maps pas encore chargé — réessayer dans 300 ms
                setTimeout(() => this._trySetup(), 300);
            }
        }
    };
}
