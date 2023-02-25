from django.db import models

# Create your models here.

STATUS = (
    ('open', 'Barcha uchun ochiq'),
    ('close', 'Barcha uchun yopiq')
)


class Issue(models.Model):
    ordinal_number = models.IntegerField(
        verbose_name="Jurnal tartib raqami",
        default=0
    )

    file_link = models.URLField(
        verbose_name="Journal manzili"
    )

    created_at = models.DateField(
        verbose_name="Kiritilgan vaqt",
    )

    class Meta:
        verbose_name = 'Jurnal soni'
        verbose_name_plural = 'Jurnallar'

    def article(self, obj):
        return obj.articles

    def __str__(self):
        return f"{self.ordinal_number} - son"


class Article(models.Model):
    issue = models.ForeignKey(
        to=Issue,
        on_delete=models.CASCADE,
        verbose_name="Jurnal soni",
        blank=True,
        null=True
    )

    status = models.CharField(
        max_length=18,
        verbose_name="Holati",
        choices=STATUS,
        default='close'
    )

    ordinal_number = models.IntegerField(
        verbose_name="Tartib raqami:",
        default=0
    )

    author_uz = models.TextField(
        verbose_name="Muallif[uz]",
        help_text="⚠️Har bir muallifni ';'(nuqtali vergul) bilan ajratib yozing"
    )

    author_en = models.TextField(
        max_length=200,
        verbose_name="Muallif[en]",
        help_text="⚠️Har bir muallifni ';'(nuqtali vergul) bilan ajratib yozing"
    )

    name_uz = models.CharField(
        max_length=200,
        verbose_name="Nomi[uz]"
    )

    name_en = models.CharField(
        max_length=200,
        verbose_name="Nomi[en]"
    )

    keywords_uz = models.TextField(
        verbose_name="Kalit so'zlar[uz]"
    )

    keywords_en = models.TextField(
        verbose_name="Kalit so'zlar[en]"
    )

    short_data_uz = models.TextField(
        verbose_name="Anatatsiya[uz]"
    )

    short_data_en = models.TextField(
        verbose_name="Anatatsiya[en]"
    )

    references = models.TextField(
        verbose_name="Foydalanilgan adabiyotlar",
        help_text="⚠️Har bir adabiyotni ';'(nuqtali vergul) bilan ajratib yozing."
    )

    file_link = models.URLField(
        verbose_name="Kitob manzili"
    )

    first_page = models.IntegerField(
        verbose_name="Birinchi sahifasi",
        default=0
    )

    last_page = models.IntegerField(
        verbose_name="Ohirgi sahifasi",
        default=0
    )

    created_at = models.DateTimeField(
        verbose_name="Kiritilgan vaqt",
        auto_now_add=True
    )

    class Meta:
        verbose_name = "Maqola"
        verbose_name_plural = "Maqolalar"

    def save(self, *args, **kwargs):
        keyword_name_word = "Bull.Inst.Math."
        try:
            set_name = f" Bull.Inst.Math., {self.created_at.year}, Vol.{self.created_at.year - 2017}, №{self.issue.ordinal_number}, PP, {self.first_page}-{self.last_page}"
            if keyword_name_word not in self.name_uz:
                self.name_uz = self.name_uz + set_name

            if keyword_name_word not in self.name_en:
                self.name_en = self.name_en + set_name
            super().save(*args, **kwargs)
        except:
            super().save(*args, **kwargs)

    def __str__(self):
        return self.name_uz
