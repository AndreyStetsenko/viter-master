<?php get_header() ?>
<?php while (have_posts()): the_post() ?>
    <main id="content" class="content">
        <div class="page-content">
            <div class="event-top">
                <div class="row">
                    <div class="col-md-auto">
                        <div class="event-poster">
                            <?php the_post_thumbnail('254x360') ?>
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="event-card">
                            <div class="event-card-date">28 июня пт. 19:00</div>
                            <div class="row">
                                <div class="col-sm">
                                    <h1 class="event-title"><?php the_title() ?></h1>
                                </div>
                                <div class="col-sm-auto">
                                    <div class="event-price"><?php the_field('price') ?>&nbsp;<?php _e('грн') ?></div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="event-city">
                                        <i class="fa fa-map-marker"></i>
                                        <?php the_field('city') ?>
                                    </div>
                                    <div class="event-place">
                                        <?php the_field('place') ?>
                                    </div>
                                </div>
                                <div class="col-sm-auto">
                                    <a href="#" class="e-btn">Купить билет</a>
                                </div>
                            </div>
                        </div>
                        <div>
                            <b>До события осталось</b>
                            <div class="time-left">
                                <b>4</b> дня <b>3</b> часа <b>29</b> минут
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ivent-content_center">
                <div class="ic_center-left">
                    <div class="iccl-d_top">
                        <div class="iccl-top">
                            <h2>Бумбокс. Олдскульная культурная программа</h2>
                            <p>Продолжительность события — 180 минут.</p>
                        </div>
                        <span class="ivent-calendar"><a id="" href="#"><i
                                        class="fa fa-calendar far-sort"></i></a><input type='text'
                                                                                       id="minMaxExample"
                                                                                       class='datepicker-here'
                                                                                       placeholder="Выбрать дату"><i
                                    class="fa fa-angle-down fal-sort"></i></span>
                    </div>
                    <div class="iccl-bottom">
                        <div class="iccl_d_left">
                            <div class="icmm-bottom-left">
                                <i class="far fa-clock" aria-hidden="true"></i>
                                <span>
								<p>19:00</p>
								<p>22 июня</p>
							</span>
                            </div>
                            <div class="icmm-bottom-left">
                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                                <span>
								<p><b>Зелёный театр</b></p>
								<p>г. Одесса, ул. Маразлиевская, 34 (ЦПКиО им. Т. Г. Шевченко)</p>
							</span>
                            </div>
                        </div>
                        <div class="ic_center-right">
                            <a class="link-to-ivent" href="/sell">Купить билет</a>
                        </div>
                    </div>
                    <hr>
                    <div class="iccl-bottom">
                        <div class="iccl_d_left">
                            <div class="icmm-bottom-left">
                                <i class="fa fa-clock" aria-hidden="true"></i>
                                <span>
								<p>19:00</p>
								<p>22 июня</p>
							</span>
                            </div>
                            <div class="icmm-bottom-left">
                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                                <span>
								<p><b>Зелёный театр</b></p>
								<p>г. Одесса, ул. Маразлиевская, 34 (ЦПКиО им. Т. Г. Шевченко)</p>
							</span>
                            </div>
                        </div>
                        <div class="ic_center-right">
                            <a class="link-to-ivent" href="/sell">Купить билет</a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="ivent-content_center">
                <h2>О событии</h2>
                <p>VIRUS Music і BIGSHOW Agency представляют:</p>
                <p>В мае—июне 2019 года Бумбокс впервые сыграет концерт с «Олдскульной культурной программой» за
                    пределами Киева.</p>
                <p>«Олдскульная культурная программа» — это программа с материалом первых трех альбомов Бумбокс —
                    «Меломанія», «Family Бизнес» и «III». На сцене, как когда-то в 2006, трое музыкантов. Звучит
                    электрический проигрыватель, гитара и вокал.</p>
                <p>В эти альбомы влюблены миллионы людей, и только несколько тысяч весной и летом смогут вживую услышать
                    программу в Днепре, Львове, Харькове и Одессе.</p>
                <p>В Одессе Бумбокс сиграет «„Олдскульную культурную программу“» 28 июня в Зелёном театре.</p>
                <p>Бумбокс уже дважды играли эту програму в Киеве, каждый раз одного концерта было мало, потому
                    анонсировали сразу двойные шоу, но и это не помогало. Олдскульная программа — это опыт
                    предварительных солдаутов, счастливых фанатских слез, категорических требований безотлагательно
                    играть «Бобік», «Гайки з Ямайки» и вспоминать «Ким ми були».</p>
            </div>

        </div>
    </main>
<?php endwhile ?>
<?php get_footer() ?>
