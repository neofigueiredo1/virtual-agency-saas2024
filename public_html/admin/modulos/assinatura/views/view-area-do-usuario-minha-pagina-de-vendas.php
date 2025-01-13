<?php
global $usuarioConta;
if ((int)$usuarioConta[0]['lp_active']!=1) {
    ob_clean();
    header("Location: /minha-conta");
    exit();
}


if (trim($usuarioConta[0]['lp_url'])=="") {
    $usuarioConta[0]['lp_url'] = Text::friendlyUrl($usuarioConta[0]['nome_completo']);
}

?>
<hr/>
<div class="card mt-3" >
    <div class="card-body fs-14" >

        <h6>Informações da página</h6>
        
        <div class="d-flex" >
            <div class="thumbnail mr-3" >
                <figure class="bg-light" ><img src="/assets/images/pix-icon.png" alt="" width="200" height="150" /></figure>
            </div>
            <div>
                
                <b>Endereço WEB:</b> <br/> 
                <span class="text-muted" >https://produtor.institutoconexo.com.br/</span><b><?php echo $usuarioConta[0]['lp_url'] ?></b><br/><br/>

                <b>Titulo:</b> <br/> 
                Nome da página <br/><br/>

                <b>Descrição:</b> <br/>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Magni eveniet, saepe repudiandae, dolorum delectus tempore, asperiores obcaecati ullam cum minus veniam quaerat quisquam aliquam laudantium magnam error a, perferendis ipsam.

            </div>
        </div>

        <hr />

        <a href="javascript:;" class="btn btn-sm btn-azul d-inline-block py-1 px-3 m-0 h-auto w-auto" style="font-size: 13px;" 
            onclick="javascript:$('.item_edit').slideUp('fast');$('.item_edit_page_info').stop().slideDown('fast');" 
        >Editar informações da página</a>
        <a href="javascript:;" class="btn btn-sm btn-azul d-inline-block py-1 px-3 m-0 h-auto w-auto" style="font-size: 13px;"
            onclick="javascript:$('.item_edit').slideUp('fast');$('.item_edit_page_header').stop().slideDown('fast');" 
        >Editar código-fonte do HEADER</a>
        <a href="javascript:;" class="btn btn-sm btn-azul d-inline-block py-1 px-3 m-0 h-auto w-auto" style="font-size: 13px;"
            onclick="javascript:$('.item_edit').slideUp('fast');$('.item_edit_page_footer').stop().slideDown('fast');" 
        >Editar código-fonte do FOOTER</a>
        <a href="/assets/vendor/grapesjs" target="_blank" class="btn btn-sm btn-azul d-inline-block py-1 px-3 m-0 h-auto w-auto" style="font-size: 13px;" >Acessar o construtor da P&aacute;gina</a>

        <style type="text/css">
            .item_edit{ display:none; }
        </style>


        <div class="item_edit item_edit_page_info" >

            <hr/>
            <h6>Informa&ccedil;&otilde;es da P&aacute;gina</h6>
            <form action="" method="POST" >
                <input type="hidden" name="lpcaction" value="lpInfoSave" >
                <div class="form-group">
                    <label for="cc_lp_url">URL da LP</label>
                    <input type="text" class="form-control" id="cc_lp_url" name="cc_lp_url" required="" value="<?php echo $usuarioConta[0]['lp_url'] ?>" />
                </div>
                <div class="form-group">
                    <label for="cc_lp_title">Titulo</label>
                    <input type="text" class="form-control" id="cc_lp_title" name="cc_lp_title" required="" value="<?php echo $usuarioConta[0]['lp_title'] ?>" />
                </div>
                <div class="form-group">
                    <label for="cc_lp_descricao" class="d-flex">
                        Descrição
                    </label>
                    <textarea class="form-control" id="cc_lp_descricao" name="cc_lp_descricao" rows="3" ><?php echo $usuarioConta[0]['lp_descricao'] ?></textarea>
                </div>
                <hr>
                <button type="submit" class="btn btn-azul-1 mb-2" >Salvar</button>
            </form>

        </div>


        <div class="item_edit item_edit_page_header" >

            <hr/>
            <h6>Código-fonte do HEADER</h6>
            <form action="" method="POST" >
                <input type="hidden" name="lpcaction" value="lpInfoHeaderSave" >
                <div class="form-group">
                    <textarea class="form-control" id="cc_lp_header" name="cc_lp_header" rows="6" ><?php echo $usuarioConta[0]['lp_header'] ?></textarea>
                </div>
                <hr>
                <button type="submit" class="btn btn-azul-1 mb-2" >Salvar</button>
            </form>

        </div>

        <div class="item_edit item_edit_page_footer" >

            <hr/>
            <h6>Código-fonte do FOOTER</h6>
            <form action="" method="POST" >
                <input type="hidden" name="lpcaction" value="lpInfoFooterSave" >
                <div class="form-group">
                    <textarea class="form-control" id="cc_lp_footer" name="cc_lp_footer" rows="6" ><?php echo $usuarioConta[0]['lp_footer'] ?></textarea>
                </div>
                <hr>
                <button type="submit" class="btn btn-azul-1 mb-2" >Salvar</button>
            </form>

        </div>



    </div>
</div>