<?php
// app/views/proposals/form.php
$isEdit = isset($proposal) && $proposal;
$pageTitle = $isEdit ? 'Editar Proposta #' . $proposal['proposal_number'] : 'Nova Proposta';

$statusOptions = [
    'rascunho'  => 'Rascunho',
    'enviado'   => 'Enviado',
    'aprovado'  => 'Aprovado',
    'reprovado' => 'Reprovado',
];

$productsJson = json_encode(array_map(fn($p) => [
    'id'          => (int)$p['id'],
    'description' => $p['description'],
    'price'       => (float)$p['price'],
    'type'        => $p['type'],
], $products));

$existingItems = $isEdit ? $items : [];
$existingItemsJson = json_encode(array_map(fn($i) => [
    'id'          => (int)$i['id'],
    'product_id'  => $i['product_id'] ? (int)$i['product_id'] : null,
    'description' => $i['description'],
    'quantity'    => (float)$i['quantity'],
    'unit_price'  => (float)$i['unit_price'],
    'total_price' => (float)$i['total_price'],
    'type'        => $i['product_type'] ?? null,
], $existingItems));

require_once __DIR__ . '/../layout_header.php';
?>

<div class="page-header">
    <h1>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-3px;margin-right:6px;color:var(--primary)"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        <?= $isEdit ? 'Editar Proposta #' . $proposal['proposal_number'] : 'Nova Proposta' ?>
    </h1>
    <div class="page-header-actions">
        <?php if ($isEdit): ?>
        <a href="<?= APP_URL ?>/proposta/<?= $proposal['public_token'] ?>" target="_blank" class="btn btn-outline btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            Link Público
        </a>
        <?php endif; ?>
        <a href="proposals.php" class="btn btn-outline btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Voltar
        </a>
    </div>
</div>

<form method="POST" action="proposals.php" id="proposalForm">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
    <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'store' ?>">
    <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= $proposal['id'] ?>"><?php endif; ?>
    <input type="hidden" name="items_json" id="items_json" value="[]">

    <!-- STEP 1 -->
    <div class="wizard-card" style="margin-bottom:20px;">
        <div class="wizard-step-header">
            <div class="wizard-step-badge">1</div>
            <div>
                <div class="wizard-step-title">Dados da Proposta</div>
                <div class="wizard-step-sub">Informações gerais e datas</div>
            </div>
            <?php if ($isEdit): ?>
                <span class="badge badge-<?= $proposal['status'] ?>" style="margin-left:auto;"><?= $statusOptions[$proposal['status']] ?></span>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group">
                    <label>Cliente *</label>
                    <select name="client_id" class="form-control" required>
                        <option value="">Selecione um cliente</option>
                        <?php foreach ($clients as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($proposal['client_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= sanitize($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <?php foreach ($statusOptions as $val => $lbl): ?>
                            <option value="<?= $val ?>" <?= ($proposal['status'] ?? 'rascunho') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full">
                    <label>Título da Proposta *</label>
                    <input type="text" name="title" class="form-control" required value="<?= sanitize($proposal['title'] ?? '') ?>" placeholder="Ex: Desenvolvimento de Site Institucional">
                </div>
                <div class="form-group">
                    <label>Data da Proposta *</label>
                    <input type="date" name="proposal_date" class="form-control" required value="<?= isset($proposal['proposal_date']) ? $proposal['proposal_date'] : date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label>Data do Follow-up</label>
                    <input type="date" name="followup_date" class="form-control" value="<?= !empty($proposal['followup_date']) ? $proposal['followup_date'] : '' ?>">
                </div>
                <div class="form-group full">
                    <label>Observações</label>
                    <textarea name="observations" class="form-control" rows="3" placeholder="Informações adicionais..."><?= sanitize($proposal['observations'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 2 -->
    <div class="wizard-card" style="margin-bottom:20px;">
        <div class="wizard-step-header">
            <div class="wizard-step-badge">2</div>
            <div>
                <div class="wizard-step-title">Produtos &amp; Serviços</div>
                <div class="wizard-step-sub">Adicione os itens desta proposta</div>
            </div>
            <div style="margin-left:auto;display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
                <span class="items-count-badge" id="itemsCountBadge">0 itens</span>
                <strong class="items-total-header" id="itemsTotalHeader">R$ 0,00</strong>
            </div>
        </div>

        <div id="itemsEmptyState" class="items-empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            <p>Nenhum item adicionado.<br><span>Use o formulário abaixo para adicionar.</span></p>
        </div>

        <div id="itemsTableSection" style="display:none;">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:90px;">Tipo</th>
                            <th>Descrição</th>
                            <th style="width:80px;text-align:center;">Qtd</th>
                            <th style="width:130px;text-align:right;">Valor Unit.</th>
                            <th style="width:130px;text-align:right;">Total</th>
                            <th style="width:46px;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody"></tbody>
                    <tfoot>
                        <tr class="items-tfoot">
                            <td colspan="4" style="text-align:right;font-weight:700;padding:13px 14px;">Total Geral</td>
                            <td style="text-align:right;font-weight:800;color:var(--primary);font-size:1rem;padding:13px 14px;" id="itemsGrandTotal">R$ 0,00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="add-item-section">
            <div class="add-item-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                Adicionar Item
            </div>
            <div class="add-item-grid">
                <div class="form-group">
                    <label>Produto/Serviço</label>
                    <select id="new_product_id" class="form-control">
                        <option value="">— Da lista cadastrada —</option>
                        <?php foreach ($products as $prod): ?>
                            <option value="<?= $prod['id'] ?>" data-description="<?= sanitize($prod['description']) ?>" data-price="<?= $prod['price'] ?>" data-type="<?= $prod['type'] ?>">
                                <?= $prod['type'] === 'produto' ? '▪ ' : '◆ ' ?><?= sanitize($prod['description']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group add-item-desc">
                    <label>Descrição *</label>
                    <input type="text" id="new_description" class="form-control" placeholder="Descreva o item">
                </div>
                <div class="form-group">
                    <label>Quantidade</label>
                    <input type="text" id="new_quantity" class="form-control" value="1" placeholder="1">
                </div>
                <div class="form-group">
                    <label>Valor Unit. (R$)</label>
                    <input type="text" id="new_unit_price" class="form-control currency-mask" placeholder="0,00">
                </div>
                <div class="form-group">
                    <label>Total</label>
                    <div class="item-total-preview" id="new_total_preview">R$ 0,00</div>
                </div>
            </div>
            <div style="margin-top:14px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                <button type="button" id="btnAddItem" class="btn btn-accent">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    Adicionar Item
                </button>
                <span id="addItemError" class="add-item-error" style="display:none;"></span>
            </div>
        </div>
    </div>

    <!-- Save bar -->
    <div class="save-proposal-bar">
        <div class="save-bar-info" id="saveBarInfo">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Adicione pelo menos 1 item para habilitar o botão Salvar.
        </div>
        <button type="submit" id="btnSave" class="btn btn-primary btn-save-proposal" disabled>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Salvar Proposta
        </button>
    </div>
</form>

<?php
$extraScripts = '
<script>
const PRODUCTS   = ' . $productsJson . ';
const IS_EDIT    = ' . ($isEdit ? 'true' : 'false') . ';
const PROPOSAL_ID= ' . ($isEdit ? (int)$proposal['id'] : 0) . ';
const CSRF       = document.querySelector("[name=csrf_token]").value;
const APP_BASE   = "' . APP_URL . '";
let localItems   = ' . $existingItemsJson . ';
let tempCounter  = -1;

function fmt(v){return "R$ "+parseFloat(v||0).toFixed(2).replace(".",",").replace(/\B(?=(\d{3})+(?!\d))/g,".");}
// Brazilian format: "1.234,56" → remove thousand dots → replace decimal comma → parse
function parseMoney(v){
    if(!v) return 0;
    // Remove spaces and R$
    v = v.replace(/R\$\s*/g,"").trim();
    // Remove thousand separators (dots before groups of 3 digits ending in comma+2digits)
    // Strategy: if contains comma, treat comma as decimal separator and dots as thousands
    if(v.indexOf(",") !== -1){
        v = v.replace(/\./g,"").replace(",",".");
    } else {
        // No comma — just a plain number like "800" or "800.00"
        // Already uses dot as decimal (standard float)
    }
    return parseFloat(v)||0;
}
function parseQty(v){return parseFloat((v||"1").replace(",","."))||0;}

function typeTag(type){
    if(type==="produto") return `<span class="item-type-tag product-tag">Produto</span>`;
    if(type==="servico") return `<span class="item-type-tag service-tag">Serviço</span>`;
    return `<span class="item-type-tag custom-tag">Avulso</span>`;
}

function renderItems(){
    const tbody    = document.getElementById("itemsTableBody");
    const emptyEl  = document.getElementById("itemsEmptyState");
    const tableEl  = document.getElementById("itemsTableSection");
    const badge    = document.getElementById("itemsCountBadge");
    const totalHdr = document.getElementById("itemsTotalHeader");
    const grandTot = document.getElementById("itemsGrandTotal");
    const saveBtn  = document.getElementById("btnSave");
    const saveInfo = document.getElementById("saveBarInfo");

    tbody.innerHTML="";
    let total=0;
    localItems.forEach(item=>{
        total+=item.total_price;
        const prod=PRODUCTS.find(p=>p.id==item.product_id);
        const tag=typeTag(prod?prod.type:(item.type||null));
        const tr=document.createElement("tr");
        tr.innerHTML=`
            <td>${tag}</td>
            <td>${item.description}</td>
            <td style="text-align:center">${item.quantity.toFixed(2).replace(".",",")}</td>
            <td style="text-align:right">${fmt(item.unit_price)}</td>
            <td style="text-align:right"><strong>${fmt(item.total_price)}</strong></td>
            <td><button type="button" class="btn-remove-item btn btn-danger btn-sm btn-icon" data-item-id="${item.id}" title="Remover">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            </button></td>`;
        tbody.appendChild(tr);
    });

    const n=localItems.length;
    emptyEl.style.display  = n>0?"none":"flex";
    tableEl.style.display  = n>0?"block":"none";
    badge.textContent      = n+(n===1?" item":" itens");
    totalHdr.textContent   = fmt(total);
    grandTot.textContent   = fmt(total);

    if(n>0){ saveBtn.disabled=false; saveBtn.classList.add("ready"); saveInfo.style.display="none"; }
    else   { saveBtn.disabled=true;  saveBtn.classList.remove("ready"); saveInfo.style.display="flex"; }
    document.getElementById("items_json").value=JSON.stringify(localItems);
}

document.getElementById("btnAddItem").addEventListener("click",()=>{
    const descEl  = document.getElementById("new_description");
    const qtyEl   = document.getElementById("new_quantity");
    const priceEl = document.getElementById("new_unit_price");
    const prodEl  = document.getElementById("new_product_id");
    const errEl   = document.getElementById("addItemError");
    errEl.style.display="none";
    const description=descEl.value.trim();
    const quantity=parseQty(qtyEl.value);
    const unit_price=parseMoney(priceEl.value);
    const product_id=parseInt(prodEl.value)||null;
    if(!description){ errEl.textContent="Preencha a descrição."; errEl.style.display="inline-flex"; descEl.focus(); return; }
    if(unit_price<=0){ errEl.textContent="Informe o valor unitário."; errEl.style.display="inline-flex"; priceEl.focus(); return; }

    const newItem={id:tempCounter--,product_id,description,quantity,unit_price,total_price:quantity*unit_price,type:null};
    if(product_id){ const p=PRODUCTS.find(x=>x.id===product_id); if(p) newItem.type=p.type; }

    if(IS_EDIT){
        const fd=new FormData();
        fd.append("csrf_token",CSRF); fd.append("action","addItem"); fd.append("proposal_id",PROPOSAL_ID);
        fd.append("product_id",product_id||""); fd.append("description",description);
        fd.append("quantity",quantity); fd.append("unit_price",unit_price.toFixed(2));
        fetch(APP_BASE+"/proposals.php",{method:"POST",body:fd}).then(()=>location.reload());
    } else {
        localItems.push(newItem); renderItems();
        prodEl.value=""; descEl.value=""; qtyEl.value="1"; priceEl.value="";
        document.getElementById("new_total_preview").textContent="R$ 0,00";
    }
});

document.getElementById("itemsTableBody").addEventListener("click",e=>{
    const btn=e.target.closest(".btn-remove-item"); if(!btn) return;
    const itemId=parseInt(btn.dataset.itemId);
    if(IS_EDIT && itemId>0){
        if(!confirm("Remover este item?")) return;
        const fd=new FormData();
        fd.append("csrf_token",CSRF); fd.append("action","deleteItem");
        fd.append("item_id",itemId); fd.append("proposal_id",PROPOSAL_ID);
        fetch(APP_BASE+"/proposals.php",{method:"POST",body:fd}).then(()=>location.reload());
    } else { localItems=localItems.filter(i=>i.id!==itemId); renderItems(); }
});

document.getElementById("new_product_id")?.addEventListener("change",function(){
    const opt=this.options[this.selectedIndex];
    const desc=opt.dataset.description||"", price=opt.dataset.price||"";
    if(desc) document.getElementById("new_description").value=desc;
    if(price){
        const priceEl = document.getElementById("new_unit_price");
        const n=parseFloat(price);
        if(!isNaN(n)){
            // Set as centesimal digits so currency-mask reads correctly
            // "800.00" → digits "80000" → mask will show "800,00"
            const digits = Math.round(n * 100).toString();
            priceEl.value = digits;
            priceEl.dispatchEvent(new Event("input"));
        }
    }
    recalcPreview();
});
function recalcPreview(){
    const qty=parseQty(document.getElementById("new_quantity")?.value||"1");
    const price=parseMoney(document.getElementById("new_unit_price")?.value||"0");
    document.getElementById("new_total_preview").textContent=fmt(qty*price);
}
document.getElementById("new_quantity")?.addEventListener("input",recalcPreview);
document.getElementById("new_unit_price")?.addEventListener("input",recalcPreview);
document.getElementById("proposalForm").addEventListener("submit",function(){ if(!IS_EDIT) document.getElementById("items_json").value=JSON.stringify(localItems); });

renderItems();
</script>';
require_once __DIR__ . '/../layout_footer.php';
?>
