# Topic Categories

`cats.html` is a snippet cut from [the topics sidenav][1]
wherein the category IDs are paired with the name.

Use `parse.php` to generate the categories selector view with
[select2][2] which requires [jQuery][3].
Because it's static content at this point, you should save the snippet
for inclusion in another page.

    php parse.php > pages/categories.select.html

  [1]:https://wordpress.uark.edu/business/
  [2]:https://select2.github.io/
  [3]:http://code.jquery.com/
