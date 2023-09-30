# restriction-Paid-Membresship-Pro
Plugin Name: Restricción de Contenido Paid Membership pro

Plugin URI: http://ajamba.org 

Description: Restringe el contenido de los en base a la fecha de la compra de la membresia.

Version: 1.0

Author: Ing. Edward Avalos

Author URI: https://www.linkedin.com/in/edward-avalos-severiche/


Descripción del Plugin

1.- En cada Nivel de Membresi agrega un campo nuevo "Días Restricción"

2.- Este campo puede tener 3 valores: 
=> valor = vacio (Sin Valor)
=> valor = 0
=> valor < 0
=> valor > 0

3.- Este plugin restringe contenido de post, custom type post, paginas que se agregan dese el administrador del plugin.

4.- Valor Vacio o Sin valor.- No restringe ningun contenido.

4.- Valor "0".- Restringe contenido con fecha de publicacion mayor o igual a la fecha que compro la membresia.

5.- Valor Menos a "0" (numeros negativos).- Restringe contenido con fecha de publicacion mayor o igual a la fecha que compro la membresia. Pero a la fecha de publicacion le resta el valor negativo o sea que puede ver contenido menor a la fecha de compra de la membresia 
=> fecha publicacion contenido >= (Fecha compra membresia - valor de dias de restriccion)

6.- Valor Mayor a "0" (numeros positivos).- restringe todo el contenido a partir de la fecha de compra, solo podra ver el contenido que se publico en el mismo dia que compro la membresia y contenido que se puglicara.

7.- En el administrador del plugin tiene un Editor de codigo Html5 que se usa para mostrar el diseño cuando el contenido esta restringido.
