-- Ensure objects are created in the application schema, not SYS
ALTER SESSION SET CONTAINER = XEPDB1;
ALTER SESSION SET CURRENT_SCHEMA = recipeasy;

create table allergens (
                           id   number(10) generated as identity,
                           name varchar2(255) not null,
                           primary key (id));
create table chefs (
                       id             number(10) generated as identity,
                       biography      clob not null,
                       profile_image  varchar2(255),
                       average_rating number(3, 2),
                       social_links   varchar2(255),
                       total_recipes  number(10),
                       is_verified    number(1) not null,
                       created_at     date default SYSDATE,
                       updated_at     date,
                       deleted_at     date,
                       user_id        number(10) not null,
                       primary key (id));
create table comments (
                          id           number(10) generated as identity,
                          comment_text clob not null,
                          created_at   date default SYSDATE,
                          deleted_at   date,
                          user_id      number(10) not null,
                          recipe_id    number(10) not null,
                          primary key (id));
create table cuisine (
                         id         number(10) generated as identity,
                         slug       varchar2(255) not null unique,
                         name       varchar2(255) not null,
                         created_at date default SYSDATE,
                         primary key (id));
create table cuisine_recipes (
                                 cuisineid number(10) not null,
                                 recipesid number(10) not null,
                                 primary key (cuisineid,
                                              recipesid));
create table favorites (
                           id         number(10) generated as identity,
                           added_at   date default SYSDATE,
                           deleted_at date,
                           user_id    number(10) not null,
                           recipe_id  number(10) not null,
                           primary key (id));
create table ingredient_allergen (
                                     ingredient_id number(10) not null,
                                     allergen_id   number(10) not null,
                                     primary key (ingredient_id,
                                                  allergen_id));
create table ingredient_categories (
                                       id          number(10) generated as identity,
                                       slug        varchar2(255) not null unique,
                                       name        varchar2(255) not null,
                                       description clob,
                                       cover_image varchar2(255),
                                       created_at  date default SYSDATE,
                                       updated_at  date,
                                       primary key (id));
create table ingredient_categories_ingredients (
                                                   ingredient_categoriesid number(10) not null,
                                                   ingredientsid           number(10) not null,
                                                   primary key (ingredient_categoriesid,
                                                                ingredientsid));
create table ingredient_recipe (
                                   ingredient_id number(10) not null,
                                   recipe_id     number(10) not null,
                                   quantity      number(10) not null,
                                   units_id      number(10) not null,
                                   primary key (ingredient_id,
                                                recipe_id));
create table ingredients (
                             id                     number(10) generated as identity,
                             slug                   varchar2(255) not null unique,
                             name                   varchar2(255) not null,
                             description            clob,
                             calories_per_100g      number(10),
                             protein_per_100g       number(10),
                             fat_per_100g           number(10),
                             carbohydrates_per_100g number(10),
                             is_active              number(1) not null,
                             created_at             date default SYSDATE,
                             deleted_at             date,
                             primary key (id));
create table media (
                       id             number(10) generated as identity,
                       file_url       varchar2(255) not null,
                       media_type     varchar2(255),
                       sort_order     number(10),
                       caption        clob,
                       uploaded_at    date,
                       recipe_step_id number(10) not null,
                       primary key (id));
create table notifications (
                               id                number(10) generated as identity,
                               message           clob not null,
                               notification_type varchar2(255),
                               target_link       varchar2(255),
                               is_read           number(1) not null,
                               created_at        date default SYSDATE,
                               user_id           number(10) not null,
                               primary key (id));
create table ratings (
                         id           number(10) generated as identity,
                         rating_value number(3, 2) not null,
                         review_text  clob,
                         created_at   date default SYSDATE,
                         deleted_at   date,
                         user_id      number(10) not null,
                         recipe_id    number(10) not null,
                         primary key (id));
create table recipe_access (
                               id         number(10) generated as identity,
                               granted_at date default SYSDATE,
                               user_id    number(10) not null,
                               recipe_id  number(10) not null,
                               primary key (id));
create table recipe_categories (
                                   id          number(10) generated as identity,
                                   slug        varchar2(255) not null unique,
                                   name        varchar2(255) not null,
                                   description clob,
                                   cover_image varchar2(255),
                                   created_at  date default SYSDATE,
                                   updated_at  date,
                                   primary key (id));
create table recipe_category_recipe (
                                        category_id number(10) not null,
                                        recipe_id   number(10) not null,
                                        primary key (category_id,
                                                     recipe_id));
create table recipe_list (
                             id          number(10) generated as identity,
                             slug        varchar2(255) not null unique,
                             name        varchar2(255) not null,
                             description clob,
                             created_at  date default SYSDATE,
                             updated_at  date,
                             deleted_at  date,
                             is_public   number(1) not null,
                             user_id     number(10) not null,
                             primary key (id));
create table recipe_list_recipe (
                                    recipe_list_id number(10) not null,
                                    recipe_id      number(10) not null,
                                    added_at       date default SYSDATE,
                                    removed_at     date,
                                    primary key (recipe_list_id,
                                                 recipe_id));
create table recipe_steps (
                              id               number(10) generated as identity,
                              title            varchar2(255),
                              step_number      number(10) not null,
                              step_text        clob not null,
                              duration_minutes number(10),
                              recipe_id        number(10) not null,
                              primary key (id));
create table recipes (
                         id                number(10) generated as identity,
                         slug              varchar2(255) not null unique,
                         title             varchar2(255) not null,
                         description       clob,
                         difficulty_level  number(10),
                         prep_time_minutes number(10),
                         cook_time_minutes number(10),
                         servings          number(10),
                         cover_image       varchar2(255),
                         view_count        number(10) default 0,
                         total_favorites   number(10) default 0,
                         average_rating    number(3, 2),
                         created_at        date default SYSDATE,
                         updated_at        date,
                         deleted_at        date,
                         chef_id           number(10) not null,
                         primary key (id));
create table roles (
                       id       number(10) generated as identity,
                       role_key varchar2(255) not null unique,
                       primary key (id));
create table tag_recipe (
                            tag_id    number(10) not null,
                            recipe_id number(10) not null,
                            primary key (tag_id,
                                         recipe_id));
create table tags (
                      id         number(10) generated as identity,
                      slug       varchar2(255) not null unique,
                      name       varchar2(255) not null,
                      created_at date default SYSDATE,
                      primary key (id));
create table units (
                       id   number(10) generated as identity,
                       name varchar2(255) not null,
                       primary key (id));
create table users (
                       id            number(10) generated as identity,
                       first_name    varchar2(255) not null,
                       last_name     varchar2(255) not null,
                       username      varchar2(255) not null unique,
                       email         varchar2(255) not null unique,
                       phone         varchar2(255) not null,
                       is_verified   number(1) not null,
                       password_hash varchar2(255) not null,
                       reset_token   varchar2(255),
                       last_login_at date,
                       created_at    date default SYSDATE,
                       updated_at    date,
                       deleted_at    date,
                       role_id       number(10) not null,
                       primary key (id));

