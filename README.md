# Custom Site Improvements for Wordpress
Ez egy (elsődlegesen magyar nyelvű) Wordpress bővítmény, aminek segítségével csinosíthatjuk a weboldalainkat. (For English scroll down)

# Funkciók:
A funkciókat a **_Megjelenés -> Custom Items Improvements Settings_** fül alatt találjátok. Valamennyi beállításhoz **_Adminisztrátori_** jogosultság szükséges.
## Lábléc
Az oldalon saját fejlesztésű láblécet alkalmazhatunk. Ehhez használhatunk egy feltöltött képet, illetve valamilyen szöveget is.
![image](https://user-images.githubusercontent.com/26169252/148642077-a106c1ca-e7de-4a86-96a9-c11db297e257.png)
- _Háttérkép_: Az alkalmazott háttérkép, ami minden oldal alján megjelenik.
- _Kép margója_: Állítható, hogy a kép milyen margókat alkalmazzon az oldalon. Negatív margó használata esetén kilóghat az oldalról. A **_Units_** beállítás lehetővé teszi, hogy pontos mértéket tudjunk használni a méretek megadásakor. (Támogatott: px, em, %, vw, vh)
- _Lábléc Felirat_: Az a felirat, ami a kép _alatt_ jelenik meg. Ha üresen hagyjuk, akkor a kép is lejjebb csúszik.
- _Szöveg elrendezése_: Elrendezhetjük a feliratot az oldal alján.
- _Szöveg margója_: A kép margójához hasolóan pontosíthatjuk a szöveg pozícióját.

# Technikai dokumentáció
A plugin 100%-ban nyílvános, ezért ha gondolod, elfogadok issue-t, illetve PR-t további változtatásokkal kapcsolatban. Kontribúcióhoz kérlek _forkold_ a repót, és _Pull Request_ formájában kérnyévezz visszatöltést.
## Új funkció közlése
Új funkciókhoz használjuk a Wordpress plugin fejlesztés paradigmáit.
- Az új funkció minden esetben _saját PHP_ fájlba kerüljön.
- Figyeljünk arra, hogy milyen jogosultságot engedünk a funkciókhoz.
- A szövegek legyenek fordításhoz alkalmazkodók (használjuk az `_e()` függvényt)
- **Kimeneteket és bemeneteket szanitáljuk!! (biztonsági rés)**
- Legyen a funkció opcionális

# Contribution
This is a Wordpress plugin that enables customization on your website (made primarily in Hungarian). If you like to contribute, please _fork_ the repository and submit a _Pull Request_ for your changes. If you like to report a bug, or request a new feature, I accept issues on this repository.
## Contribution guide
If you want to make a new feature, please consider the following Wordpress paradigms:
- A new function should have it's _own PHP_ file.
- Make sure, that you set the proper permissions for your function
- Every string, or text should be translatable (use the `_e()` function)
- **Inputs and outputs must be sanitized!! (for security reasons)**
- Function should be optional (togglable)
