export const marketComponent = (market, marketIndex) => {
  const dbMarket = market.db_market;
  let isStatusEnabled = "checked";
  let isLocked = 0;
  let marketId = 0;
  let disabled = '';
  let disabledStatus = 'pointer-events-none';
  let gameStatus = '';

  if (dbMarket) {    
    isStatusEnabled = dbMarket.status ? "checked" : "";
    isLocked = dbMarket.locked == 0 ? "checked" : "";
    marketId = dbMarket.id;
    disabled = ((dbMarket.result_declared == 1) || (dbMarket.game?.status == 4) || (dbMarket.game?.status == 2))? 'pointer-events-none' : '';
    disabledStatus = disabled;
    gameStatus = (dbMarket.game?.status == 4) ? 'Completed' : ((dbMarket.result_declared == 1) ? 'Outcome Declared' : ((dbMarket.game?.status == 2)? "Cancelled" : ""));
  }

  return `<div class="col-sm-12 singleMarket ${disabled}" data-index="${marketIndex}" data-key="${market.key}" data-market-type="${market?.market_type == "custom" ? "custom" : market.key}">
    <input type="hidden" name="market[${marketIndex}][key]" value="${market.key}" >
    <input type="hidden" name="market[${marketIndex}][market_type]" value="${market?.market_type == "custom" ? "custom" : market.key}">
    <input type="hidden" name="market[${marketIndex}][id]" value="${marketId}">
    <input type="hidden" name="market[${marketIndex}][outcome_type]" value="${ market.outcome_type}">
    <input type="hidden" name="market[${marketIndex}][player_props]" value="${ market.player_props}">
    <input type="hidden" name="market[${marketIndex}][game_period_market]" value="${ market.game_period_market}">
    <div class="card">
        <div class="card-header d-flex justify-content-between gap-3  flex-wrap align-items-center relative">
            <div>
                <h6 class="card-title mb-0">
                    ${market.name}
                </h6>
                <p class="text-muted fs-13">${market.description}</p>
            </div>

            <div class="btn-container d-flex gap-2 flex-shrink-0 align-items-center">
                <div class="d-flex justify-content-center align-items-center gap-3 ${disabledStatus}">
                ${ (gameStatus != "")? `<span class="badge badge--success py-1">${gameStatus}</span>` : "" }
                    <label for="market-status-${marketIndex}" class="switch-lock switchMarketStatus" data-id="${marketId}" >
                        <input type="checkbox" id="market-status-${marketIndex}" name="market[${marketIndex}][status]" value="1" ${isStatusEnabled}>
                        <span class="switch-lock-one"> <span class="switch-circle"></span> <i class="fas fa-eye-slash"></i></span>
                        <span class="switch-lock-two"> <i class="fas fa-eye"></i> <span class="switch-circle"></span> </span>
                    </label>
                    
                    <label for="market-lock-${marketIndex}" class="switch-lock switchMarketLock ${disabled}" data-id="${marketId}">
                        <input type="checkbox" id="market-lock-${marketIndex}" name="market[${marketIndex}][locked]" value="1" ${isLocked}>
                        <span class="switch-lock-one"> <span class="switch-circle"></span> <i class="fas fa-lock"></i></span>
                        <span class="switch-lock-two"> <i class="fas fa-lock-open"></i> <span class="switch-circle"></span> </span>
                    </label>
                </div>
                
                <button type="button" class="btn bg-transparent text--dark p-1 collapseBtn pointer-events-auto">
                    <i class="la la-angle-up m-0"></i>
                </button>

            </div>
        </div>
        <div class="card-body d-flex flex-column gap-3">
            ${
              market.need_market_title
                ? `<input type="text" class="form-control" name="market[${marketIndex}][title]" value="${
                    market.title ? market.title : ""
                  }" placeholder="Write the title here">`
                : `<input type="${(market?.market_type == "custom")? 'text' : 'hidden'}" name="market[${marketIndex}][title]" value="${
                    market?.market_type == "custom" ? market.name : ""
                  }" class="form-control">`
            }
        </div>
    </div>
  </div>`;
};

export const marketRemoveButton = () => {
  return `<button type="button" class="btn btn-sm btn--danger removeMarketBtn"><i class="la la-times m-0"></i></button>`;
};

export const totalsOutcome = ({
  marketIndex,
  index,
  name,
  label,
  odds,
  point,
  status,
  locked,
  id = 0,
}) => {
  marketIndex = marketIndex.trim();
  odds = odds ? parseFloat(odds) : "";
  point = point ? parseFloat(Math.abs(point)) : "";

  return `<div class="form-group flex-grow-1 singleOutcome mb-0" data-index="${index}">
            ${outcomesLockComponent(marketIndex, index, label, status, locked)}
            <input type="hidden" name="market[${marketIndex}][outcomes][${index}][outcome_id]" value="${id}">
            <div class="input-group-wrapper">
                <div class="input-group">
                    <span class="input-group-text">Odds</span>
                    <input type="hidden" name='market[${marketIndex}][outcomes][${index}][name]' value='${name}'>
                    <input type="number" name="market[${marketIndex}][outcomes][${index}][odds]" step="any" value="${parseFloat(odds)}" class="form-control" placeholder="0.00" required>
                </div>

                 <div class="input-group">
                    <span class="input-group-text">
                        <small class="small-title">Point</small>
                    </span>
                    <input type="number" step="any" name="market[${marketIndex}][outcomes][${index}][point]" value="${point}" class="form-control spreadPointInput" placeholder="0.00" required>
                </div>
            </div>
        </div>
    `;
};

export const oddsOnlyOutcome = ({
  marketIndex,
  index,
  name,
  label,
  odds,
  status = 1,
  locked = 0,
  id = 0
}) => {
  marketIndex = marketIndex.trim();
  odds = odds ? parseFloat(odds) : "";

  return `
        <div class="form-group flex-grow-1 mb-0 singleOutcome" data-index="${index}">
            ${outcomesLockComponent(marketIndex, index, label, status, locked)}
            <input type="hidden" name="market[${marketIndex}][outcomes][${index}][outcome_id]" value="${id}">
            ${
              name
                ? `<input type="hidden" name='market[${marketIndex}][outcomes][${index}][name]' value='${
                    name ?? ""
                  }' placeholder="Outcome">`
                : ""
            }

            
            <div class="input-group-wrapper">
                <div class="input-group">
                    ${!name ? `<span class="input-group-text">Outcome</span>` : ""}

                    ${
                      !name
                        ? `<input type="text" class="form-control" name='market[${marketIndex}][outcomes][${index}][name]' value='${
                            name ?? ""
                          }' placeholder="Outcome">`
                        : ""
                    }

                    <span class="input-group-text">Odds</span>
                    <input type="number" name='market[${marketIndex}][outcomes][${index}][odds]' step="any" value="${odds}" class="form-control" placeholder="0.00" required>
                </div>
            </div>
        </div>
    `;
};

export const spreadOutcome = ({
  marketIndex,
  index,
  name,
  label,
  odds,
  point,
  status = 1,
  locked = 0,
  id = 0,
}) => {
  marketIndex = marketIndex.trim();
  let pointType = "";
  if (point) {
    pointType = point >= 0 ? "+" : "-";
  }

  odds = odds ? parseFloat(odds) : "";
  point = point ? parseFloat(Math.abs(point)) : "";

  return `
        <div class="form-group flex-grow-1 mb-0 singleOutcome" data-index="${index}">
            
            ${outcomesLockComponent(marketIndex, index, label, status, locked)}
            <input type="hidden" name="market[${marketIndex}][outcomes][${index}][outcome_id]" value="${id}">
            <div class="input-group-wrapper">
                <div class="input-group">
                    <span class="input-group-text">Odds</span>
                    <input type="hidden" name="market[${marketIndex}][outcomes][${index}][name]" value="${name}">
                    <input type="number" name="market[${marketIndex}][outcomes][${index}][odds]" step="any" value="${odds}" class="form-control" placeholder="0.00" required>
                </div>

                <div class="input-group">
                    <span class="input-group-text">
                        <small class="small-title">Point</small>
                        <select name="market[${marketIndex}][outcomes][${index}][point_type]" class="form-control form-select border-0 p-0 h-auto pointTypeSelect">
                            <option value="+" ${
                              pointType == "+" ? "selected" : ""
                            }> + </option>
                            <option value="-" ${
                              pointType == "-" ? "selected" : ""
                            }> - </option>
                        </select>
                    </span>
                    <input type="number" step="any" name="market[${marketIndex}][outcomes][${index}][point]" value="${point}" class="form-control spreadPointInput" placeholder="0.00" required>
                </div>
            </div>
        </div>
    `;
};

export const outcomesComponent = (key) =>
  `<div class="outcomes position-relative d-flex gap-3 flex-wrap "  data-key="${key}"></div>`;

export const addNewOutComeButton = (market) =>
  `<button type="button" data-market='${JSON.stringify(
    market
  )}' class="btn btn-outline--dark d-block addNewOutcome"> <i class="la la-plus"></i> Add More</button>`;

function outcomesLockComponent(
  marketIndex,
  index,
  label,
  status = 1,
  locked = 0,
  name = "",
  custom = false
) {
  let labelValue = `${label ? `<label class="mb-0">${label}</label>` : ""}`;
  if (custom && label == "") {
    labelValue = `<div class="w-75 input-group-wrapper mb-2">
          <div class="input-group">
            <span class="input-group-text">Outcome</span>
            <input type="text" name='market[${marketIndex}][outcomes][${index}][name]' value='${name}' class="form-control " required>
          </div>
      </div>`;
  }
  return `
        <div class="d-flex justify-content-between align-items-center">
            ${labelValue}
            <div class="ms-auto">
                <label for="outcome-status-${marketIndex}-${index}" class="switch-lock">
                    <input id="outcome-status-${marketIndex}-${index}" type="checkbox" name="market[${marketIndex}][outcomes][${index}][status]" ${
    status ? "checked" : ""
  } value="1">
                    <span class="switch-lock-one"> <span class="switch-circle"></span> <i class="fas fa-eye-slash"></i></span>
                    <span class="switch-lock-two"> <i class="fas fa-eye"></i> <span class="switch-circle"></span> </span>
                </label>
                
                <label for="outcome-lock-${marketIndex}-${index}" class="switch-lock">
                    <input id="outcome-lock-${marketIndex}-${index}" type="checkbox" name="market[${marketIndex}][outcomes][${index}][locked]" ${
    locked == 0 ? "checked" : ""
  } value="1">
                    <span class="switch-lock-one"> <span class="switch-circle"></span> <i class="fas fa-lock"></i></span>
                    <span class="switch-lock-two"> <i class="fas fa-lock-open"></i> <span class="switch-circle"></span> </span>
                </label>
            </div>
        </div>`;
}

export const totalsCustomOutcome = ({
  marketIndex,
  index,
  name,
  label,
  odds,
  point,
  status,
  locked,
  id = 0,
}) => {
  marketIndex = marketIndex.trim();
  odds = odds ? parseFloat(odds) : "";
  point = point ? parseFloat(Math.abs(point)) : "";

  return `<div class="form-group flex-grow-1 singleOutcome mb-0" data-index="${index}">
      ${outcomesLockComponent(
        marketIndex,
        index,
        label,
        status,
        locked,
        name,
        true
      )}
      <input type="hidden" name="market[${marketIndex}][outcomes][${index}][outcome_id]" value="${id}">
      <div class="input-group-wrapper">
      <div class="input-group">
              <span class="input-group-text">Odds</span>
              <input type="number" name="market[${marketIndex}][outcomes][${index}][odds]" step="any" value="${parseFloat(
    odds
  )}" class="form-control" placeholder="0.00" required>
          </div>

            <div class="input-group">
              <span class="input-group-text">
                  <small class="small-title">Point</small>
              </span>
              <input type="number" step="any" name="market[${marketIndex}][outcomes][${index}][point]" value="${point}" class="form-control spreadPointInput" placeholder="0.00" required>
          </div>
      </div>
  </div>`;
};

export const oddsOnlyCustomOutcome = ({
  marketIndex,
  index,
  name,
  label,
  odds,
  status = 1,
  locked = 0,
  id = 0,
}) => {
  marketIndex = marketIndex.trim();
  odds = odds ? parseFloat(odds) : "";

  return `<div class="form-group flex-grow-1 mb-0 singleOutcome" data-index="${index}">
      <div class="float-end">
        ${outcomesLockComponent(marketIndex, index, label, status, locked)}
      </div>
      <input type="hidden" name="market[${marketIndex}][outcomes][${index}][outcome_id]" value="${id}">
      <div class="input-group-wrapper">
          <div class="input-group">
              <span class="input-group-text">Outcome</span>
              <input type="text" class="form-control" name='market[${marketIndex}][outcomes][${index}][name]' value='${
    name ?? ""
  }' placeholder="Outcome">
              <span class="input-group-text">Odds</span>
              <input type="number" name='market[${marketIndex}][outcomes][${index}][odds]' step="any" value="${odds}" class="form-control" placeholder="0.00" required>
          </div>
      </div>
  </div>`;
};

export const spreadCustomOutcome = ({
  marketIndex,
  index,
  name,
  label,
  odds,
  point,
  status = 1,
  locked = 0,
  id = 0,
}) => {
  marketIndex = marketIndex.trim();
  let pointType = "";
  if (point) {
    pointType = point >= 0 ? "+" : "-";
  }

  odds = odds ? parseFloat(odds) : "";
  point = point ? parseFloat(Math.abs(point)) : "";

  return `<div class="form-group flex-grow-1 mb-0 singleOutcome" data-index="${index}">
      ${outcomesLockComponent(
        marketIndex,
        index,
        label,
        status,
        locked,
        name,
        true
      )}
      <input type="hidden" name="market[${marketIndex}][outcomes][${index}][outcome_id]" value="${id}">
      <div class="input-group-wrapper">
          <div class="input-group">
              <span class="input-group-text">Odds</span>
              <input type="number" name="market[${marketIndex}][outcomes][${index}][odds]" step="any" value="${odds}" class="form-control" placeholder="0.00" required>
          </div>
          <div class="input-group">
              <span class="input-group-text">
                  <small class="small-title">Point</small>
                  <select name="market[${marketIndex}][outcomes][${index}][point_type]" class="form-control form-select border-0 p-0 h-auto pointTypeSelect">
                      <option value="+" ${
                        pointType == "+" ? "selected" : ""
                      }> + </option>
                      <option value="-" ${
                        pointType == "-" ? "selected" : ""
                      }> - </option>
                  </select>
              </span>
              <input type="number" step="any" name="market[${marketIndex}][outcomes][${index}][point]" value="${point}" class="form-control spreadPointInput" placeholder="0.00" required>
          </div>
      </div>
  </div>`;
};
