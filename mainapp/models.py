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

    author_uz = models.CharField(
        max_length=200,
        verbose_name="Muallif[uz]"
    )

    author_en = models.CharField(
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

    keywords_uz = models.CharField(
        max_length=300,
        verbose_name="Kalit so'zlar[uz]"
    )

    keywords_en = models.CharField(
        max_length=300,
        verbose_name="Kalit so'zlar[en]"
    )

    short_data_uz = models.CharField(
        max_length=400,
        verbose_name="Anatatsiya[uz]"
    )

    short_data_en = models.CharField(
        max_length=400,
        verbose_name="Anatatsiya[en]"
    )

    references = models.CharField(
        verbose_name="Foydalanilgan adabiyotlar",
        max_length=500
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

    def __str__(self):
        return self.name_uz
