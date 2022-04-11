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
