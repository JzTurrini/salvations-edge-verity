const localData = {
  shapes: {
    inside: {
      left: null,
      middle: null,
      right: null,
    },
    outside: {
      left: null,
      middle: null,
      right: null,
    },
  },
  shapesToReset: {
    inside: {
      left: null,
      middle: null,
      right: null,
    },
    outside: {
      left: null,
      middle: null,
      right: null,
    },
  },
  shapesToAbbrv: {
    Cubo: "QQ",
    Pirâmide: "TT",
    Esfera: "CC",
    Cone: "CT",
    Cilindro: "CQ",
    Prisma: "QT",
  },
  reset: () => {
    // Atualiza todas as variáveis para o valor inicial.
    localData.shapes = { ...localData.shapesToReset };
    // Limpa os steps caso já informados.
    $(".show-symbol").removeClass("active");
    $(".show-symbol").removeClass("inactive");
    $(".step-by-step").html("Aguardando você selecionar todas as opções...");
  },
  areAllShapesFilled() {
    const shapes = localData.shapes;

    for (let area in shapes) {
      for (let position in shapes[area]) {
        if (shapes[area][position] === null) {
          return false;
        }
      }
    }
    return true;
  },
  getSteps() {
    const formData = {
      leftInside: localData.shapes.inside.left,
      middleInside: localData.shapes.inside.middle,
      rightInside: localData.shapes.inside.right,
      leftOut: localData.shapes.outside.left,
      middleOut: localData.shapes.outside.middle,
      rightOut: localData.shapes.outside.right,
    };

    $.ajax({
      url: "script.php", // O caminho para o seu script PHP
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        let steps = response; // Supondo que a resposta JSON tenha uma chave 'steps'
        let htmlContent = "<ul>";

        steps.forEach(function (step) {
          htmlContent += "<li>" + step + "</li>";
        });

        htmlContent += "</ul>";
        $(".step-by-step").html(htmlContent);
      },
      error: function (xhr, status, error) {
        $(".step-by-step").html("<p>" + error + "</p>");
      },
    });
  },
  toogleEquals(action, area, component) {
    if (area === "inside") {
      if (action === "active") {
        $(`[data-component=${component}]:not(.active)`).addClass("inactive");
      } else {
        $(`[data-component=${component}]`).removeClass("inactive");
      }
    }
  },
  getAllowedComponents: function () {
    // Based on similar function of: https://necrondow.github.io/Encounter4.html
    // All credits to its developer.
    const selected = $(
      `.symbol-group[data-area="outside"] .show-symbol.active`
    );

    let components = {
      C: 2,
      Q: 2,
      T: 2,

      Allow: function (components) {
        let myComponents = {
          C: (components.match(/C/g) || []).length,
          Q: (components.match(/Q/g) || []).length,
          T: (components.match(/T/g) || []).length,
        };

        for (const counter in myComponents) {
          if (this[counter] < myComponents[counter]) {
            return false;
          }
        }

        return true;
      },
    };

    selected.each(function () {
      for (let component of $(this).data("component")) {
        components[component]--;
      }
    });

    return components;
  },
};

$(function () {
  localData.reset();

  $(".show-symbol").on("click", function () {
    const button = $(this);
    const isInactive = button.hasClass("inactive");
    if (!isInactive) {
      // Dados para a lógica do negócio.
      const parentDiv = button.closest(".symbol-group");
      const wasSelected = button.hasClass("active");
      const shape = button.data("shape");
      const component = button.data("component");
      const area = parentDiv.data("area");
      const position = parentDiv.data("position");

      parentDiv.find(".show-symbol.active").each(function () {
        const element = $(this);
        element.removeClass("active");
        localData.toogleEquals("inactive", "inside", element.data("component"));
      });

      if (!wasSelected) {
        button.addClass("active");
        localData.toogleEquals("active", area, component);
        localData.shapes[area][position] = shape;
      } else {
        localData.shapes[area][position] = null;
        localData.toogleEquals("inactive", area, component);
      }

      // Quando a área for outside.
      if (area == "outside") {
        let components = localData.getAllowedComponents();

        $(`.symbol-group[data-area='outside'] .show-symbol:not(.active)`).each(
          function () {
            if (!components.Allow($(this).data("component"))) {
              $(this).addClass("inactive");
            } else {
              $(this).removeClass("inactive");
            }
          }
        );
      }

      // Aqui eu verifico se todos os shapes estão preenchidos, em caso positivo, eu requisito os steps.
      if (localData.areAllShapesFilled()) {
        localData.getSteps();
      } else {
        $(".step-by-step").html(
          "Aguardando você selecionar todas as opções..."
        );
      }
    }
  });

  $(".btn-reset").on("click", function (e) {
    e.preventDefault();
    localData.reset();
    window.scrollTo({ top: 0 });
  });
});
