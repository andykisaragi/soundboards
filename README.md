# Random Soundboards

Laravel / Inertia / Vue app. Search the Freesound API & populate a grid of 9 clickable sounds. Save & load soundboards.

## How

npm & composer install
get Freesound API key https://freesound.org/docs/api/authentication.html
copy .env.example to .env and populate FREESOUND_API_KEY

## Why

I chose the Freesound API because I've had an idea for a while to use it's geospatial data for a project to generate soundscapes based on the position of the ISS. 

This is _not_ that project but it was a reason to finally look at the API.

## Tradeoffs / improvements

I was intending to add a fruit-machine like 'hold' button to the sounds so that you could fix some and reload the others, but I ran out of time.

The app doesn't do any complex querying of the API, for this app all we really needed was the search term (though I am adding a 0-5 seconds length limit)

The Laravel structure is fairly simple - in a larger app I wouldn't have everything in one controller and would tend to have a service layer for model operations
