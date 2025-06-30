jQuery(document).ready(function ($) {
  const $form = $("#rentalForm");
  const $totalPriceElement = $("#totalPrice");
  const $produtoContainer = $(".produto-container");

  const precoBase = parseFloat($produtoContainer.data("preco-base"));
  const precoExtra = parseFloat($produtoContainer.data("preco-extra"));
  const dataEntrega = document.getElementById("dataEntrega");
  const horaEntrega = document.getElementById("horaEntrega");
  const dataRetorno = document.getElementById("dataRetorno");
  const horaRetorno = document.getElementById("horaRetorno");
  let precoSeguro = parseFloat($("#seguroSim").val());
  let taxesValue = 0;
  let precoFinalSemTaxa = 0;

  let flatRates = [];
  try {
    flatRates = JSON.parse($produtoContainer.attr("data-flat-rates"));
  } catch (e) {}

  /*if (horaEntrega !== null && horaRetorno !== null) { 
    horaEntrega.setAttribute("min", "08:00");
    horaEntrega.setAttribute("max", "20:00");
    horaRetorno.setAttribute("min", "08:00");
    horaRetorno.setAttribute("max", "20:00");
  }*/

  // Cria uma data para amanhã
  const today = new Date();
  const tomorrow = new Date();
  tomorrow.setDate(today.getDate() + 1);

  if (dataEntrega !== null && dataRetorno !== null) {
    const tomorrowStr = tomorrow.toISOString().split("T")[0];
    dataEntrega.setAttribute("min", tomorrowStr);
    dataRetorno.setAttribute("min", tomorrowStr);
  }

  $form.on("change", calcularPreco);

  $('input[name="local_entrega"]').on("change", function () {
    if ($(this).val() === "resort") {
      $("#selectResortBoxEntrega").show();
      $("#inputEspecificoBoxEntrega").hide();
    } else {
      $("#selectResortBoxEntrega").hide();
      $("#inputEspecificoBoxEntrega").show();
    }
  });
  $('input[name="local_retorno"]').on("change", function () {
    if ($(this).val() === "resort") {
      $("#selectResortBoxRetorno").show();
      $("#inputEspecificoBoxRetorno").hide();
    } else {
      $("#selectResortBoxRetorno").hide();
      $("#inputEspecificoBoxRetorno").show();
    }
  });

function calcularPreco() {
    let dataEntrega = $("#dataEntrega").val();
    let dataRetorno = $("#dataRetorno").val();
    let horaEntrega = $("#horaEntrega").val();
    let horaRetorno = $("#horaRetorno").val();
    let acessoriosTotal = 0;
    let acessoriosSelecionados = [];
    precoSeguro = parseFloat($("#seguroSim").val());

    let diasAluguel = Math.ceil(
      (new Date(dataRetorno) - new Date(dataEntrega)) / (1000 * 60 * 60 * 24)
    );

    if (flatRates.length > 0) {
      let precoFlat = 0;
      for (let i = 0; i < flatRates.length; i++) {
        let dias = parseInt(flatRates[i].dias);
        if (diasAluguel <= dias) {
          precoFlat = parseFloat(flatRates[i].preco);
          console.log(`Flat rate found for ${dias} days: $${precoFlat}`);
          break;
        }else{
          precoFlat = flatRates[i].preco + (diasAluguel - dias) * precoExtra;
          console.log(`Flat rate not found for ${diasAluguel} days, using base price: $${precoFinalSemTaxa}`);
        }
      }
      // if (precoFlat === 0 && flatRates.length > 0) {
      //   precoFlat = parseFloat(flatRates[flatRates.length - 1].preco);
      //   console.log(`Using last flat rate for ${flatRates[flatRates.length - 1].dias} days: $${precoFlat}`);
      // }
      precoFinalSemTaxa = precoFlat;
    } else {
      precoFinalSemTaxa = precoBase;
      if (diasAluguel > 3) {
        precoFinalSemTaxa = precoFinalSemTaxa + (diasAluguel - 3) * precoExtra;
      }
    }

    if ($("#seguroSim").is(":checked")) {
      precoFinalSemTaxa += precoSeguro;
    } else {
      precoSeguro = 0;
    }

    $(".acessorioCheck:checked").each(function () {
      let valor = parseFloat($(this).data("valor"));
      acessoriosTotal += valor;
      acessoriosSelecionados.push({
        nome: $(this).val(),
        valor: valor,
      });
    });

    precoFinalSemTaxa += acessoriosTotal;

    taxesValue = precoFinalSemTaxa * 0.06;
    let precoFinalComTaxa = precoFinalSemTaxa + taxesValue;

    // Exibe os valores no HTML
    $("#totalSemTaxa strong").text(`US$ ${precoFinalSemTaxa.toFixed(2)}`);
    $("#taxesValue strong").text(`US$ ${taxesValue.toFixed(2)}`);
    $("#totalPrice strong").text(`US$ ${precoFinalComTaxa.toFixed(2)}`);
  }

  $form.on("submit", function (e) {
    e.preventDefault();
    let dataEntrega = $("#dataEntrega").val();
    let dataRetorno = $("#dataRetorno").val();
    let horaEntrega = $("#horaEntrega").val();
    let horaRetorno = $("#horaRetorno").val();
    let sobreNome = $("#sobrenome").val();

    let produtoID = $produtoContainer.data("produto-id");
    let precoTotal = $totalPriceElement
      .text()
      .replace("US$ ", "")
      .replace(",", ".");
    let acessorios = [];
    $(".acessorioCheck:checked").each(function () {
      acessorios.push({
        nome: $(this).val(),
        valor: parseFloat($(this).data("valor")),
      });
    });
    console.log(acessorios);

    precoTotal = parseFloat(precoTotal);

    // Verificar local de entrega
    let tipoEntrega = $('input[name="local_entrega"]:checked').val();
    let localEntrega =
      tipoEntrega === "resort"
        ? $("#selectResortBoxEntrega select").val()
        : $("#inputEspecificoBoxEntrega input").val();

    // Verificar local de retorno
    let tipoRetorno = $('input[name="local_retorno"]:checked').val();
    let localRetorno =
      tipoRetorno === "resort"
        ? $("#selectResortBoxRetorno select").val()
        : $("#inputEspecificoBoxRetorno input").val();

    // (opcional) validação
    if (!localEntrega || !localRetorno) {
      alert("Por favor, preencha o local de entrega e o local de retorno.");
      return;
    }

    // Remove any existing modal
    $("#customModal").remove();

    $.ajax({
      url: rentalAjax.ajaxurl,
      type: "POST",
      data: {
      action: "add_product_to_cart",
      produto_id: produtoID,
      preco: precoFinalSemTaxa + taxesValue, // total with tax
      preco_final_sem_taxa: precoFinalSemTaxa, // total without tax
      taxes: taxesValue, // send taxes separately
      local_entrega: localEntrega,
      local_retorno: localRetorno,
      security: rentalAjax.nonce,
      data_entrega: dataEntrega,
      hora_entrega: horaEntrega,
      data_retorno: dataRetorno,
      hora_retorno: horaRetorno,
      sobrenome: sobreNome,
      seguro: precoSeguro,
      acessorios: acessorios,
      },
      dataType: "json",
      xhrFields: {
      withCredentials: true
      },
      success: function (result) {
      if (result.success) {
        // Create modal
        const modalHtml = `
        <div id="customModal" style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;">
          <div style="background:#fff;padding:30px 20px;border-radius:8px;max-width:90vw;box-shadow:0 2px 16px rgba(0,0,0,0.2);text-align:center;">
          <p style="font-size:1.1em;margin-bottom:20px;">Product added to cart!</p>
          <button id="modalOkBtn" style="padding:8px 24px;font-size:1em;border:none;background:#0073aa;color:#fff;border-radius:4px;cursor:pointer;">OK</button>
          </div>
        </div>
        `;
        $("body").append(modalHtml);
        $("#modalOkBtn").on("click", function () {
        $("#customModal").remove();
        window.location.href = "/cart/";
        });
      } else {
        // Error modal
        const modalHtml = `
        <div id="customModal" style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;">
          <div style="background:#fff;padding:30px 20px;border-radius:8px;max-width:90vw;box-shadow:0 2px 16px rgba(0,0,0,0.2);text-align:center;">
          <p style="font-size:1.1em;margin-bottom:20px;">Error adding to cart.</p>
          <button id="modalOkBtn" style="padding:8px 24px;font-size:1em;border:none;background:#0073aa;color:#fff;border-radius:4px;cursor:pointer;">OK</button>
          </div>
        </div>
        `;
        $("body").append(modalHtml);
        $("#modalOkBtn").on("click", function () {
        $("#customModal").remove();
        });
      }
      },
      error: function (xhr, status, error) {
      // Error modal
      const modalHtml = `
        <div id="customModal" style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;">
        <div style="background:#fff;padding:30px 20px;border-radius:8px;max-width:90vw;box-shadow:0 2px 16px rgba(0,0,0,0.2);text-align:center;">
          <p style="font-size:1.1em;margin-bottom:20px;">Unexpected error adding the product.</p>
          <button id="modalOkBtn" style="padding:8px 24px;font-size:1em;border:none;background:#0073aa;color:#fff;border-radius:4px;cursor:pointer;">OK</button>
        </div>
        </div>
      `;
      $("body").append(modalHtml);
      $("#modalOkBtn").on("click", function () {
        $("#customModal").remove();
      });
      },
    });
  });

  // --- BLOCK UNAVAILABLE DAYS WITH FLATPICKR ---
  // Adiciona CSS customizado para flatpickr
  if (!document.getElementById('flatpickr-custom-style')) {
    const style = document.createElement('style');
    style.id = 'flatpickr-custom-style';
    style.innerHTML = `
      .flatpickr-day.flatpickr-day--unavailable, .flatpickr-day.flatpickr-disabled {
        background: #ff3b3b !important;
        color: #fff !important;
        border-radius: 50% !important;
        position: relative;
        cursor: not-allowed !important;
        opacity: 1 !important;
      }
      .flatpickr-day.flatpickr-day--unavailable:hover::after {
        content: 'Not working day';
        position: absolute;
        left: 50%;
        top: 110%;
        transform: translateX(-50%);
        background: #ff3b3b;
        color: #fff;
        padding: 2px 10px;
        border-radius: 6px;
        font-size: 13px;
        white-space: nowrap;
        z-index: 99;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
      }
      .flatpickr-calendar {
        font-family: 'Montserrat', Arial, sans-serif;
        font-size: 16px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.18);
      }
      .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
        background: #ff6a00 !important;
        color: #fff !important;
      }
      .flatpickr-day.today {
        border-color: #ff6a00 !important;
      }
    `;
    document.head.appendChild(style);
  }

  // Mensagem flutuante para dia bloqueado
  function showUnavailableDayMessage(input) {
    if (document.getElementById('unavailableDayMsg')) return;
    const msg = document.createElement('div');
    msg.id = 'unavailableDayMsg';
    msg.innerText = 'This day is not available for pickup or delivery.';
    msg.style.position = 'fixed';
    msg.style.top = '30px';
    msg.style.left = '50%';
    msg.style.transform = 'translateX(-50%)';
    msg.style.background = '#ff3b3b';
    msg.style.color = '#fff';
    msg.style.padding = '12px 28px';
    msg.style.borderRadius = '8px';
    msg.style.fontSize = '1.1em';
    msg.style.zIndex = '99999';
    msg.style.boxShadow = '0 2px 12px rgba(0,0,0,0.18)';
    document.body.appendChild(msg);
    setTimeout(() => {
      msg.remove();
    }, 2500);
  }

  let unavailableDays = [];
  function setupFlatpickrWithUnavailable(dateInput) {
    if (!dateInput || typeof flatpickr === 'undefined') return;
    if (dateInput._flatpickr) {
      dateInput._flatpickr.destroy(); // Remove instancia anterior
    }
    flatpickr(dateInput, {
      minDate: new Date(Date.now() + 24 * 60 * 60 * 1000), // tomorrow
      disable: unavailableDays, // array de strings 'YYYY-MM-DD'
      dateFormat: 'Y-m-d',
      locale: {
        firstDayOfWeek: 1
      },
      onDayCreate: function(dObj, dStr, fp, dayElem) {
        if (dayElem.dateObj) {
          const ymd = dayElem.dateObj.toISOString().split('T')[0];
          if (unavailableDays.includes(ymd)) {
            dayElem.classList.add('flatpickr-day--unavailable');
            dayElem.title = 'Not working day';
          }
        }
      },
      onChange: function(selectedDates, dateStr, instance) {
        // Se o usuário tentar selecionar um dia bloqueado, mostra mensagem e limpa input
        if (unavailableDays.includes(dateStr)) {
          instance.clear();
          showUnavailableDayMessage(dateInput);
        }
      },
      onValueUpdate: function(selectedDates, dateStr, instance) {
        // Previne seleção via digitação manual
        if (unavailableDays.includes(dateStr)) {
          instance.clear();
          showUnavailableDayMessage(dateInput);
        }
      }
    });
  }
  // Fetch blocked days from backend
  $.ajax({
    url: rentalAjax.ajaxurl,
    type: 'POST',
    data: { action: 'get_unavailable_days' },
    dataType: 'json',
    success: function (resp) {
      if (resp.success && Array.isArray(resp.data)) {
        unavailableDays = resp.data;
        setupFlatpickrWithUnavailable(document.getElementById('dataEntrega'));
        setupFlatpickrWithUnavailable(document.getElementById('dataRetorno'));
      }
    }
  });
});
