=== Widget Indicadores Econ&oacute;micos (Chile) ===
Contributors: Cristhopher Riquelme
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KJMRNVD96DLZA
Tags: chile, indicadores, economicos, uf, ivp, dolar, euro, ipc, utm, imacec, tpm, libra de cobre, tasa desempleo
Requires at least: 3.0.1
Tested up to: 4.5
Stable tag: 2.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Muestra los principales indicadores econ&oacute;micos para Chile.
UF, IVP, D&oacute;lar, Euro, IPC, UTM, IMACEC, TPM, Libra de Cobre, Tasa de desempleo

== Description ==

Un widget que entrega (a elecci&oacute;n del administrador) los principales indicadores econ&oacute;micos para Chile.
Estos se actualizan diariamente y se guardan en la propia base de datos de wordpress, agilizando en gran medida las peticiones al servidor y la carga de los indicadores.

http://www.mindicador.cl/ API REST que entrega los principales indicadores econ&oacute;micos del d&iacute;a y tambi&eacute;n los hist&oacute;ricos.

== Installation ==

1. Descomprime el archivo y sube la carpeta a "/wp-content/plugins/"
2. Activa el plugin en la opci&oacute;n "Plugins" del men&uacute; de wordpress
3. En la opci&oacute;n "Widgets" toma y arrastra el widget "Indicadores Econ&oacute;micos (Chile)" a tu sidebar o al &aacute;rea donde deseas visualizarlo

Nota: T&uacute; decides si quieres dejarle el dise&ntilde;o que trae por defecto o si quieres dise&ntilde;arlo a tu gusto.

== Frequently Asked Questions ==

= &iquest;Qu&eacute; indicadores econ&oacute;micos entrega? =
Los valores actuales de la UF, IVP, D&oacute;lar, Euro, IPC, UTM, IMACEC, TPM, Libra de Cobre, y la Tasa de desempleo.

= No muestra o no actualiza los indicadores econ&oacute;micos  =
Puede que el servidor donde tienes instalado wordpress tenga desactivada la funci&oacute;n "file_get_contents" o deshabilitada la directiva "allow_url_fopen" en php.ini

== Screenshots ==

1. Opciones del Widget
2. Dise&ntilde;o por defecto del widget

== Changelog ==

= 2.5 =
* Se corrige error con el prefijo de tabla al cambiar el que wordpress usa por defecto.

= 2.4 =
* Se corrigen errores en las unidades de medida del TPM y Libra de Cobre.

= 2.3 =
* Se corrigen las unidades de medida seg&uacute;n indicador.

= 2.2 =
* Se corrigi&oacute; un problema con el manejo del cach&eacute;.

= 2.1 =
* Se agregaron 3 nuevos indicadores.

= 2.0 =
* Cambios de importancia en el c&oacute;digo que actualiza los indicadores.
* Se agregaron los valores de 3 indicadores m&aacute;s.
* Correcci&oacute;n de errores reportados.

= 1.6 =
* Correcci&oacute;n de errores al mostrar indicadores.

= 1.5 =
* Estructura HTML del widget modificada
* Se agreg&oacute; el t&iacute;tulo del widget y la fecha actual
* Opci&oacute;n para habilitar/deshabilitar el dise&ntilde;o que trae por defecto
* Modificaciones menores al c&oacute;digo

= 1.2 =
* Integraci&oacute;n de cURL para actualizar indicadores en caso de no poder utilizar la funci&oacute;n "file_get_contents"

= 1.0 =
* Primera versi&oacute;n del plugin

== Upgrade Notice ==

= 2.5 =
Se corrige el problema (que presentaban algunos usuarios) al mostrar el listado de indicadores econ&oacute;micos en la configuraci&oacute;n del widget.

= 2.4 =
Se muestra la unidad de medida correcta para el TPM y Libra de Cobre.

= 2.3 =
Se muestra la unidad de medida correcta para el IPC e Imacec.

= 2.2 =
Se corrigi&oacute; un problema con el manejo del cach&eacute; el cual provocaba que a veces se mostraran los valores desfasados en un d&iacute;a.

= 2.1 =
Se agreg&oacute; el soporte para 3 nuevos indicadores econ&oacute;micos.
TPM, Libra de Cobre, y la Tasa de desempleo.

= 2.0 =
Se cambi&oacute; la forma de obtener los indicadores econ&oacute;micos por el consumo de una API REST creada para este fin. (http://www.mindicador.cl/)
Tambi&eacute;n se agregaron otros indicadores faltantes: IVP, IPC, e IMACEC.

= 1.6 =
Correcci&oacute;n de errores que presentaban los indicadores en algunos casos, cuando la fuente agrega un nuevo dato con el "D&oacute;lar Observado" del d&iacute;a de ma&ntilde;ana.

= 1.5 =
Varias modificaciones importantes adem&aacute;s de &uacute;tiles, especificadas en el Changelog.

= 1.2 =
Ahora el script se puede conectar mediante cURL para actualizar los indicadores en caso de que el servidor tenga deshabilitada la directiva "allow_url_fopen".

= 1.0 =
This version fixes a security related bug. Upgrade immediately.