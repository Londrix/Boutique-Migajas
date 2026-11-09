/**
 * ============================================================
 * BOUTIQUE MIGAJAS - Inyector del Footer
 * Archivo: src/components/footer.js
 *
 * Estrategia robusta:
 *   - Construye la URL absoluta del componente usando
 *     document.currentScript o import.meta (fallback a ruta relativa).
 *   - Registra en consola cada etapa para facilitar depuracion.
 *   - Funciona correctamente bajo XAMPP en rutas profundas.
 * ============================================================
 */

(function () {
  'use strict';

  /* ----------------------------------------------------------
     RUTA DEL COMPONENTE
     Usamos una URL absoluta calculada desde la ubicacion del
     propio script para que funcione sin importar desde donde
     se sirva la pagina (XAMPP, Live Server, subdirectorios).
  ---------------------------------------------------------- */
  var FOOTER_URL = (function () {
    // Intentar obtener la ubicacion real del script en ejecucion
    var scriptEl = document.currentScript;
    if (scriptEl && scriptEl.src) {
      // Reemplaza "footer.js" por "Footer.html" en la misma carpeta
      return scriptEl.src.replace('footer.js', 'Footer.html');
    }
    // Fallback: ruta relativa desde la raiz del documento
    return 'src/components/Footer.html';
  })();

  var CONTAINER_ID = 'footer-container';

  /* ----------------------------------------------------------
     PUNTO DE ENTRADA
  ---------------------------------------------------------- */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadFooter);
  } else {
    loadFooter();
  }

  /* ----------------------------------------------------------
     CARGA E INYECCION
  ---------------------------------------------------------- */
  function loadFooter() {
    var container = document.getElementById(CONTAINER_ID);

    if (!container) {
      console.warn('[BM Footer] No se encontro #' + CONTAINER_ID + '. Agrega <div id="footer-container"></div> antes de </body>.');
      return;
    }

    console.log('[BM Footer] Cargando desde:', FOOTER_URL);

    fetch(FOOTER_URL)
      .then(function (response) {
        if (!response.ok) {
          throw new Error('HTTP ' + response.status + ' al cargar ' + FOOTER_URL);
        }
        return response.text();
      })
      .then(function (html) {
        container.innerHTML = html;
        console.log('[BM Footer] Componente inyectado correctamente.');
        setFooterYear();
        initNewsletterForm();
      })
      .catch(function (error) {
        console.error('[BM Footer] Error:', error.message);
        // Fallback minimo para que la pagina no quede cortada
        container.innerHTML =
          '<footer class="bm-footer" aria-label="Pie de pagina">' +
            '<div class="bm-footer__bottom" style="justify-content:center;padding:2rem;">' +
              '<p class="bm-footer__copy">' +
                '&copy; ' + new Date().getFullYear() + ' Boutique Migajas &middot; Todos los derechos reservados.' +
              '</p>' +
              '<p class="bm-footer__copy" style="font-size:0.7rem;color:#f87171;margin-top:.5rem;">' +
                'Footer no pudo cargarse. Verifica la consola del navegador (F12).' +
              '</p>' +
            '</div>' +
          '</footer>';
      });
  }

  /* ----------------------------------------------------------
     ANO DINAMICO
  ---------------------------------------------------------- */
  function setFooterYear() {
    var yearEl = document.getElementById('footer-year');
    if (yearEl) {
      yearEl.textContent = new Date().getFullYear();
    }
  }

  /* ----------------------------------------------------------
     NEWSLETTER
     Se llama despues de inyectar el HTML para que los
     elementos del formulario ya existan en el DOM.
  ---------------------------------------------------------- */
  function initNewsletterForm() {
    var form   = document.getElementById('newsletter-form');
    var msgEl  = document.getElementById('newsletter-msg');

    if (!form) {
      console.warn('[BM Footer] No se encontro #newsletter-form dentro del footer.');
      return;
    }

    form.addEventListener('submit', function (event) {
      event.preventDefault();

      var emailInput = document.getElementById('newsletter-email');
      var email      = emailInput ? emailInput.value.trim() : '';

      if (!email || !emailInput.validity.valid) {
        showMessage(msgEl, 'Por favor ingresa un correo valido.', 'error');
        if (emailInput) emailInput.focus();
        return;
      }

      var submitBtn = form.querySelector('.bm-footer__newsletter-btn');
      var originalText = submitBtn ? submitBtn.textContent : '';

      if (submitBtn) {
        submitBtn.disabled    = true;
        submitBtn.textContent = 'Enviando...';
      }

      /*
       * TODO: Reemplazar mockNewsletterApi() por fetch real:
       *
       *   fetch('/api/newsletter', {
       *     method:  'POST',
       *     headers: { 'Content-Type': 'application/json' },
       *     body:    JSON.stringify({ email: email })
       *   })
       *   .then(function(res) { ... })
       *   .catch(function(err) { ... });
       */
      mockNewsletterApi(email)
        .then(function () {
          showMessage(msgEl, 'Gracias! Te has suscrito correctamente.', 'success');
          if (emailInput) emailInput.value = '';
        })
        .catch(function (err) {
          showMessage(msgEl, 'Algo salio mal. Intentalo de nuevo.', 'error');
          console.error('[BM Footer] Newsletter error:', err);
        })
        .finally(function () {
          if (submitBtn) {
            submitBtn.disabled    = false;
            submitBtn.textContent = originalText;
          }
        });
    });
  }

  /* ----------------------------------------------------------
     HELPERS
  ---------------------------------------------------------- */
  function showMessage(el, text, type) {
    if (!el) return;
    el.textContent = text;
    el.style.color = type === 'error' ? '#f87171' : '#86efac';
    setTimeout(function () { el.textContent = ''; }, 5000);
  }

  /** Simulacion - eliminar cuando el backend este listo */
  function mockNewsletterApi(email) {
    return new Promise(function (resolve) {
      setTimeout(function () {
        console.info('[BM Footer] Mock suscripcion:', email);
        resolve();
      }, 700);
    });
  }

})();
