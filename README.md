# POKEDECK

Pokedeck is an API for generate pokemon’s decks with the following rules :
- A deck contain exactly 60 cards 
- 12-16 pokemon of a given type 
- 10 energy cards for that type 
- The rest of the deck is filled with random trainer cards where no more than 4 card of the same kind can be repeated

### API Endpoints
| Endpoint                   | Verb |                                                              |
|----------------------------|------|--------------------------------------------------------------|
| /api/decks                 | GET  | List previous generated decks                                |
| /api/decks/generate        | GET  | Generate a new deck                                          |
| /api/decks/generate/{type} | GET  | Generate a new deck with specified type ( For Exemple Fire ) |
| /api/decks/{uuid}          | GET  | Show specified cards deck                                |

### API Key
You can add your API key in .env file with the key POKEMON_API_KEY
```
    POKEMON_API_KEY=xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
```

### Install
After git the repo you can just do in the root of this repo :
```
./vendor/bin/sail up
```
Add an API key if you have one ( see above ), it’s optional it work without api key
if you are gently with the number of requests.

Next, just call API Endpoint with local host and your favorite API Requester ( Insomnia, Postman ...)
```
http://localhost/api/decks/generate
```
