from django.db import models

# Create your models here.


class Issue(models.Model):
    name_uz = models.CharField(
        max_length=200,
        verbose_name="Nomi[uz]"
    )

    name_en = models.CharField(
        max_length=200,
        verbose_name="Nomi[en]"
    )

    file_link = models.URLField(
        verbose_name="Journal manzili"
    )

    created_at = models.DateTimeField(
        verbose_name="Kiritilgan vaqt",
        auto_now_add=True
    )

    class Meta:
        verbose_name = 'Jurnal soni'
        verbose_name_plural = 'Jurnallar'

    def article(self, obj):
        return obj.articles

    def __str__(self):
        return self.name_uz


class Article(models.Model):
    issue = models.ForeignKey(
        to=Issue,
        on_delete=models.CASCADE,
        verbose_name="Jurnal soni"
    )

    author_uz = models.TextField(
        verbose_name="Muallif[uz]"
    )

    author_en = models.TextField(
        max_length=200,
        verbose_name="Muallif[en]"
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

    def __str__(self):
        return self.name_uz
