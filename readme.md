![Laravel](https://laravel.com/assets/img/components/logo-laravel.svg)

<h1 align="center">Genealogy Application</h1>

> **Development in progress**  
> In development progress, any changes of table structure **will be updated** directly to corresponding **migration file**.
>
> [Baca README Bahasa Indonesia](readme.id.md)

## About
Genealogy (Silsilah) application to record our family members.

## Features
This application uses Bahasa Indonesia and Sundanese based on `config.locale`.

### Logic Concept
1. A person can have one father
2. A person can have one mother
3. A person can have one parent (couple of mother and father)
4. A person can have 0 to many childrens
5. A person can have 0 to many spouses (husbands or wife)
6. A couple can have 0 to many childrens (based on parent_id)

### Family Member Entry
1. Enter Name and Gender
2. Set Father
3. Set Mother
4. Add Spouse
5. Add Child

### Person Attribute
1. Nickname
2. Gender
3. Fullname
4. Date of birht
5. Date of death (or at least year of death)
6. Address
7. Phone Number
8. Email

## How to Install
1. Clone the repo : `git clone https://github.com/hildafdan/kopi.git`
2. `cd kopi`
3. `composer install`
4. `cp .env.example .env`
5. `php artisan key:generate`
6. Create **database on MySQL**
7. **Set database credentials** on `.env` file
8. `php artisan migrate`
9. `php artisan serve`
10. Done (Register as new user to start using the application).

## Testing
This application built with testing (TDD) using in-memory sqlite database.
```bash
$ vendor/bin/phpunit
```

## Contributing
Feel free to submit Issue for bugs or sugestions and Pull Request.

## Screenshots

## License
The Laravel framework is open-sourced software licensed under the [MIT license](LICENSE).
