# Al timbrar con un texto `&amp;` devuelve `705 - XML Estructura inválida`

## Descripción

Al momento de enviar una solicitud de timbrado (métodos `stamp`) Finkok verifica la existencia de `&amp`,
y en caso de encontrarla lo reconoce como un error de tipo `705 - XML Estructura inválida`.

Esto es un error, pues un texto válido como `Camiseta con la leyenda: &amp; is XML` es rechazado.
El texto anterior, codificado como XML es `Camiseta con la leyenda: &amp;amp; is XML`.

La validación es arbitraria en su naturaleza, dado que en ningún momento el SAT en su documentación técnica
(*Anexo 20*, *Matriz de errores* o *Guías de llenado*) ha establecido la invalidez de esta información.

Si bien la existencia de tener en un texto de origen la cadena de caracteres `&amp;` *supone un error de codificación*,
no necesariamente se trata de un error. También es cierto que, al generar un CFDI con un texto que no se apegue
fielmente a la operación que refleja, puede ser una causa para considerarlo con errores.

Es probable que si el texto es `Piezas de pl&aacute;stico` se trate de un *error de codificación*,
donde en realidad el texto debería ser `Piezas de plástico`.
Siendo totalmente estrictos, el texto del CFDI no es correcto y no refleja fielmente la operación.

Un texto como `P&amp;G` termina codificándose como `P&amp;amp;G`, y es *muy probable* que se trate de un error.
Un texto como `P&G` termina codificándose como `P&amp;G`, y no es un error.

También es importante considerar que, dada la probabilidad de que el texto `&amp;` aparezca en un texto es muy baja,
nuestros sistemas informáticos deberían poder validar estos casos y/o hacer las correcciones oportunas para que
se envíen textos que reflejen fielmente las operaciones, sin errores de codificación.

Personalmente, considero que esto se trata de una situación que se puede prevenir e incluso corregir sin causar grandes
inconvenientes a los usuarios. Esto no se trata de un error ortográfico, se trata de un problema de codificación.

Por otro lado, también considero que si un CFDI contiene estos errores es un tema entre el SAT, el emisor y el receptor.
Pero no es un tema del PAC. Sobre todo: **el PAC no debería establecer validaciones sin fundamento legal**.

## Reporte

Se reportó a Finkok en el [ticket #97947](https://support.finkok.com/support/tickets/97947) en donde se explica el caso.

### Respuesta 2024-03-13

La respuesta inicial ha sido que, al momento de entregar el CFDI firmado al SAT, el SAT lo recibe,
pero genera una incidencia para el PAC, y estas incidencias se consideran una métrica negativa.

Se les ha solicitado compartir la evidencia de esta incidencia (anonimizada), pero se está evaluando la petición.

También Finkok se ha comprometido a levantar el caso con el SAT, para poder timbrar sin que esta situación sea
considerada una incidencia, en cuyo caso podrían omitir la validación.

### Pruebas 2024-04-12

He corrido pruebas de integración y he encontrado que ya no se está realizando la validación al momento de timbrar.
Al parecer los reportes levantados por la comunidad han tenido un buen resultado.

Si Finkok decide quitar la validación está haciendo bien al mantenerse al margen de no validar sin fundamento legal.
Ahora corresponde a los usuarios de su servicio hacer las correctas validaciones y prevenir entradas con `&amp;`,
que terminan en el CFDI como `&amp;amp;`, y que en realidad seguramente se tratan de *errores de codificación*
